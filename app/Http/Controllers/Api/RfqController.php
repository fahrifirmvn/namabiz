<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    /**
     * Get list of RFQs for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $rfqs = [
            [
                'id' => 1042,
                'supplier_name' => 'Agrim PTE Ltd',
                'product_name' => 'Black pepper (Grade 1)',
                'volume' => '20 MT',
                'status' => 'quote_received',
                'quote_fob' => '$6,420/MT',
                'valid_until' => 'Valid 48h',
                'created_at' => now()->subDay()->toIso8601String(),
            ],
            [
                'id' => 1041,
                'supplier_name' => 'Nusantara Agro',
                'product_name' => 'Desiccated coconut (High Fat)',
                'volume' => '5 MT',
                'status' => 'pending',
                'quote_fob' => null,
                'valid_until' => null,
                'created_at' => now()->subDays(3)->toIso8601String(),
            ],
        ];

        return ApiResponse::success([
            'rfqs' => $rfqs,
        ], 'RFQs retrieved successfully');
    }

    /**
     * Create a new Request for Quotation (RFQ).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'supplier_ids' => ['required', 'array', 'min:1'],
            'supplier_ids.*' => ['integer'],
            'product_name' => ['required', 'string'],
            'volume_mt' => ['required', 'numeric', 'min:0.1'],
            'target_incoterm' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $newRfqId = rand(1043, 9999);

        return ApiResponse::success([
            'rfq_id' => $newRfqId,
            'status' => 'submitted',
            'suppliers_contacted' => count($validated['supplier_ids']),
            'message' => 'Your RFQ has been dispatched to suppliers. You will receive quotes shortly.',
        ], 'RFQ submitted successfully', 201);
    }
}
