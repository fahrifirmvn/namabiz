<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get aggregate data for the mobile Home Screen.
     */
    public function home(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user();

        $savedCount = $user ? SavedSupplier::where('user_id', $user->id)->count() : 12;

        $needsAttention = [
            [
                'id' => 'alert-1',
                'type' => 'compliance_risk',
                'title' => 'Nusantara Agro — Halal MUI certificate expires in 18 days',
                'tag' => 'Compliance Risk',
                'audited_info' => "Audited Oct '24",
                'action_label' => 'Review',
                'supplier_slug' => 'nusantara-agro',
            ],
            [
                'id' => 'alert-2',
                'type' => 'quote_received',
                'title' => 'Agrim PTE Ltd sent a quote for RFQ #1042',
                'details' => '(Black pepper, 20 MT)',
                'badge' => 'FOB $6,420/MT',
                'valid_until' => 'Valid 48h',
                'action_label' => 'View Quote',
                'supplier_slug' => 'agrim-pte-ltd',
            ],
            [
                'id' => 'alert-3',
                'type' => 'rfq_pending',
                'title' => 'RFQ #1041 to Nusantara Agro — no response',
                'action_label' => 'Nudge',
                'supplier_slug' => 'nusantara-agro',
            ],
        ];

        $recentlyViewed = Supplier::query()
            ->with(['tags', 'documents', 'certifications.certification'])
            ->take(5)
            ->get();

        return ApiResponse::success([
            'membership' => [
                'tier' => $user->subscription_tier ?? 'Business Pro',
                'renews_date' => $user?->premium_expires_at?->format('M d, Y') ?? 'Dec 14, 2026',
                'is_active' => true,
            ],
            'quick_stats' => [
                'saved' => $savedCount,
                'comparing' => 2,
                'catalogs_downloaded' => 18,
            ],
            'needs_attention' => $needsAttention,
            'recently_viewed' => SupplierResource::collection($recentlyViewed),
        ], 'Home dashboard retrieved successfully');
    }
}
