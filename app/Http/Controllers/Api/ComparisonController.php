<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Support\ApiResponse;
use App\Support\PremiumAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComparisonController extends Controller
{
    /**
     * Compare 2 to 3 suppliers side-by-side.
     */
    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'supplier_ids' => ['required', 'array', 'min:2', 'max:3'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
        ]);

        $supplierIds = array_values(array_unique($validated['supplier_ids']));

        $suppliers = Supplier::query()
            ->with(['tags', 'documents', 'certifications.certification'])
            ->whereIn('id', $supplierIds)
            ->get();

        return ApiResponse::success([
            'suppliers' => SupplierResource::collection($suppliers),
            'compared_count' => $suppliers->count(),
        ], 'Suppliers comparison generated successfully');
    }

    /**
     * Download comparison PDF (Premium only).
     */
    public function downloadPdf(Request $request): Response
    {
        $user = $request->user('sanctum') ?? $request->user();
        if (! PremiumAccess::isPremium($user)) {
            return response()->json([
                'success' => false,
                'message' => 'This action requires a Premium subscription.',
                'paywall' => true,
            ], 403);
        }

        $validated = $request->validate([
            'supplier_ids' => ['required', 'array', 'min:2', 'max:3'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
        ]);

        $supplierIds = array_values(array_unique($validated['supplier_ids']));

        $suppliers = Supplier::query()
            ->with(['certifications.certification', 'documents', 'tags'])
            ->whereIn('id', $supplierIds)
            ->get();

        $pdf = Pdf::loadView('analytics.compare-pdf', compact('suppliers'));

        return $pdf->download('supplier-comparison.pdf');
    }
}
