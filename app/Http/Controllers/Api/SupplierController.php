<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Support\ApiResponse;
use App\Support\SqlLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a paginated listing of suppliers with search and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query()->with(['tags']);

        if ($request->filled('q') || $request->filled('search')) {
            $rawKeyword = is_scalar($request->input('q', $request->input('search')))
                ? (string) $request->input('q', $request->input('search'))
                : '';
            $pattern = SqlLike::pattern($rawKeyword);

            if ($pattern !== null) {
                $query->where(function ($q) use ($pattern) {
                    $q->where('name', 'like', $pattern)
                        ->orWhere('industry_category', 'like', $pattern)
                        ->orWhere('country', 'like', $pattern)
                        ->orWhere('description', 'like', $pattern);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('country') && is_string($request->input('country'))) {
            $query->where('country', mb_substr($request->input('country'), 0, 100));
        }

        if ($request->filled('category') && is_string($request->input('category'))) {
            $query->where('industry_category', mb_substr($request->input('category'), 0, 100));
        }

        if ($request->filled('certification_id') && is_numeric($request->input('certification_id'))) {
            $certId = (int) $request->input('certification_id');
            $query->whereHas('certifications', function ($q) use ($certId) {
                $q->where('certification_id', $certId);
            });
        }

        $sortBy = is_string($request->input('sort_by')) ? $request->input('sort_by') : 'relevance';
        match ($sortBy) {
            'newest' => $query->latest(),
            'company_size' => $query->orderByDesc('company_size'),
            default => $query->orderByDesc('is_verified')->orderBy('name'),
        };

        $perPage = max(1, min((int) $request->input('per_page', 12), 50));
        $suppliers = $query->paginate($perPage);

        return ApiResponse::success(
            SupplierResource::collection($suppliers),
            'Suppliers retrieved successfully',
            200,
            [
                'pagination' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                    'has_more' => $suppliers->hasMorePages(),
                ],
            ]
        );
    }

    /**
     * Display the specified supplier by ID or slug.
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        $supplier = Supplier::query()
            ->with(['tags', 'documents', 'certifications.certification'])
            ->where(function ($q) use ($idOrSlug) {
                $q->where('slug', $idOrSlug);
                if (ctype_digit($idOrSlug)) {
                    $q->orWhere('id', (int) $idOrSlug);
                }
            })
            ->first();

        if (! $supplier) {
            return ApiResponse::error('Supplier not found.', 404);
        }

        return ApiResponse::success(
            new SupplierResource($supplier),
            'Supplier details retrieved successfully'
        );
    }

    /**
     * Resolve raw QR scan string from CameraX into a supplier record.
     */
    public function qrResolve(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:500'],
        ]);

        $rawCode = trim($request->input('code'));
        $slugOrId = $rawCode;

        // Check if QR contains a full URL, e.g. https://domain.com/suppliers/atlas-packaging
        if (filter_var($rawCode, FILTER_VALIDATE_URL)) {
            $path = parse_url($rawCode, PHP_URL_PATH);
            $segments = array_values(array_filter(explode('/', (string) $path)));
            $slugOrId = end($segments) ?: $rawCode;
        } elseif (str_contains($rawCode, '://')) {
            // Check if deep link scheme: supplychainpro://supplier/atlas-packaging
            $parts = explode('/', $rawCode);
            $slugOrId = end($parts) ?: $rawCode;
        }

        $supplier = Supplier::query()
            ->with(['tags', 'documents', 'certifications.certification'])
            ->where(function ($q) use ($slugOrId) {
                $q->where('slug', $slugOrId);
                if (ctype_digit((string) $slugOrId)) {
                    $q->orWhere('id', (int) $slugOrId);
                }
            })
            ->first();

        if (! $supplier) {
            return ApiResponse::error('Supplier not found for the provided QR code.', 404);
        }

        return ApiResponse::success(
            new SupplierResource($supplier),
            'Supplier resolved successfully from QR code'
        );
    }

    /**
     * Toggle bookmark/saved status for the authenticated user.
     */
    public function toggleSave(Request $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (! $supplier) {
            return ApiResponse::error('Supplier not found.', 404);
        }

        $userId = $request->user()->id;
        $saved = SavedSupplier::where('user_id', $userId)
            ->where('supplier_id', $supplier->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
            $message = 'Supplier removed from collection.';
        } else {
            SavedSupplier::create([
                'user_id' => $userId,
                'supplier_id' => $supplier->id,
            ]);
            $isSaved = true;
            $message = 'Supplier saved to collection.';
        }

        return ApiResponse::success([
            'is_saved' => $isSaved,
            'supplier_id' => $supplier->id,
        ], $message);
    }

    /**
     * Get all saved suppliers for the authenticated user.
     */
    public function savedSuppliers(Request $request): JsonResponse
    {
        $savedSuppliers = Supplier::query()
            ->whereHas('savedBy', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->with(['tags', 'documents', 'certifications.certification'])
            ->latest()
            ->get();

        return ApiResponse::success(
            SupplierResource::collection($savedSuppliers),
            'Saved suppliers collection retrieved successfully'
        );
    }

    /**
     * Update supplier details (for web admin & sync).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (! $request->user()?->can('access-admin')) {
            return ApiResponse::error('Forbidden.', 403);
        }

        $supplier = Supplier::find($id);
        if (! $supplier) {
            return ApiResponse::error('Supplier not found', 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash'],
            'logo' => ['nullable', 'string', 'max:500', 'url:http,https'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'country' => ['sometimes', 'required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'industry_category' => ['sometimes', 'required', 'string', 'max:100'],
            'is_verified' => ['sometimes', 'boolean'],
            'moq' => ['nullable', 'string', 'max:100'],
            'production_capacity' => ['nullable', 'string', 'max:100'],
            'avg_lead_time' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255', 'url:http,https'],
        ]);

        $supplier->update($validated);

        return ApiResponse::success(new SupplierResource($supplier), 'Supplier updated successfully');
    }
}
