<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Comparison;
use App\Models\Document;
use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Support\ApiResponse;
use App\Support\PremiumAccess;
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
        $isPremium = PremiumAccess::isPremium($user);

        $savedCount = $user ? SavedSupplier::where('user_id', $user->id)->count() : 0;

        $latestComparison = $user
            ? Comparison::where('user_id', $user->id)->latest()->first()
            : null;
        $comparingCount = is_array($latestComparison?->supplier_ids)
            ? count($latestComparison->supplier_ids)
            : 0;

        $availableCatalogs = Document::query()
            ->when(! $isPremium, fn ($q) => $q->where('is_premium', false))
            ->count();

        $recentlyViewed = Supplier::query()
            ->with(['tags', 'documents', 'certifications.certification'])
            ->withUserSavedStatus($user)
            ->latest('updated_at')
            ->take(5)
            ->get();

        $needsAttention = $recentlyViewed
            ->take(3)
            ->map(function (Supplier $supplier): array {
                $hasWhatsapp = filled($supplier->whatsapp_url);

                return [
                    'id' => 'supplier-'.$supplier->id,
                    'type' => $hasWhatsapp ? 'contact_available' : 'supplier_update',
                    'title' => $hasWhatsapp
                        ? $supplier->name.' siap dihubungi via WhatsApp'
                        : $supplier->name.' memperbarui profil & katalog',
                    'details' => $supplier->industry_category.' · '.$supplier->country,
                    'badge' => $supplier->is_verified ? 'Verified' : null,
                    'valid_until' => $supplier->updated_at?->diffForHumans(),
                    'action_label' => 'Lihat Profil',
                    'supplier_slug' => $supplier->slug,
                ];
            })
            ->values()
            ->all();

        $tierLabel = match (true) {
            $user === null => 'Guest',
            $isPremium => 'Business Pro',
            default => ucfirst((string) ($user->subscription_tier ?: 'free')),
        };

        $response = ApiResponse::success([
            'membership' => [
                'tier' => $tierLabel,
                'renews_date' => $isPremium ? ($user?->premium_expires_at?->format('M d, Y') ?? 'Aktif') : 'Paket Gratis',
                'is_active' => $isPremium,
            ],
            'quick_stats' => [
                'saved' => $savedCount,
                'comparing' => $comparingCount,
                'catalogs_downloaded' => $availableCatalogs,
            ],
            'needs_attention' => $needsAttention,
            'recently_viewed' => SupplierResource::collection($recentlyViewed),
        ], 'Home dashboard retrieved successfully');

        $scope = 'user:'.($user?->id ?? 'guest').'|tier:'.($user?->subscription_tier ?? 'guest');

        return ApiResponse::withConditionalCache(
            request: $request,
            response: $response,
            scope: $scope,
            isPublic: false,
            maxAge: 0
        );
    }
}
