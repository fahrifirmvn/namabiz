<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Support\ApiResponse;
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
            $keyword = $request->input('q', $request->input('search'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('industry_category', 'like', "%{$keyword}%")
                    ->orWhere('country', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->input('country'));
        }

        if ($request->filled('category')) {
            $query->where('industry_category', $request->input('category'));
        }

        if ($request->filled('certification_id')) {
            $query->whereHas('certifications', function ($q) use ($request) {
                $q->where('certification_id', $request->input('certification_id'));
            });
        }

        $sortBy = $request->input('sort_by', 'relevance');
        match ($sortBy) {
            'newest' => $query->latest(),
            'company_size' => $query->orderByDesc('company_size'),
            default => $query->orderByDesc('is_verified')->orderBy('name'),
        };

        $perPage = min((int) $request->input('per_page', 12), 50);
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
            ->where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
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
            'code' => ['required', 'string'],
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
            ->where('slug', $slugOrId)
            ->orWhere('id', $slugOrId)
            ->first();

        if (! $supplier) {
            return ApiResponse::error("Supplier with code '{$rawCode}' not found.", 404);
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
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return ApiResponse::error('Supplier not found', 404);
        }

        $supplier->update($request->only([
            'name', 'slug', 'logo', 'tagline', 'description',
            'country', 'city', 'industry_category', 'is_verified',
            'moq', 'production_capacity', 'avg_lead_time', 'website',
        ]));

        return ApiResponse::success(new SupplierResource($supplier), 'Supplier updated successfully');
    }
}
