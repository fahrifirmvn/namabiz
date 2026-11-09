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
                'type' => 'supplier_update',
                'title' => 'Agrim PTE Ltd updated their catalog & certifications',
                'details' => '(Black pepper, 20 MT available)',
                'badge' => 'Verified',
                'valid_until' => 'Updated today',
                'action_label' => 'View Profile',
                'supplier_slug' => 'agrim-pte-ltd',
            ],
            [
                'id' => 'alert-3',
                'type' => 'contact_available',
                'title' => 'Direct WhatsApp contact verified for PT Sumber Logistik',
                'action_label' => 'Contact',
                'supplier_slug' => 'pt-sumber-logistik',
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
