<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Support\PremiumAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum') ?? $request->user();
        $isPremium = PremiumAccess::isPremium($user);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->resolveLogoUrl(),
            'tagline' => $this->tagline,
            'description' => $this->description,
            'country' => $this->country,
            'city' => $this->city,
            'industry_category' => $this->industry_category,
            'is_verified' => (bool) $this->is_verified,
            'about' => $this->about,
            'website' => $this->website,
            'whatsapp' => $this->whatsapp,
            'whatsapp_url' => $this->whatsapp_url,
            'email' => $this->email,
            'is_saved' => $this->resolveIsSaved($user),

            // Premium operational data (masked if user is not premium)
            'premium_data' => [
                'is_locked' => ! $isPremium,
                'founded_year' => $isPremium ? $this->founded_year : null,
                'company_size' => $isPremium ? $this->company_size : null,
                'production_capacity' => $isPremium ? $this->production_capacity : null,
                'moq' => $isPremium ? $this->moq : null,
                'avg_lead_time' => $isPremium ? $this->avg_lead_time : null,
                'carbon_footprint_report' => $isPremium ? $this->carbon_footprint_report : null,
            ],

            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('tag_name')),
            'certifications' => $this->whenLoaded('certifications', function () use ($isPremium) {
                return $this->certifications->map(function ($sc) use ($isPremium) {
                    $isLocked = $sc->is_premium_field && ! $isPremium;

                    return [
                        'id' => $sc->certification_id,
                        'name' => $sc->certification?->name,
                        'category' => $sc->certification?->category,
                        'status' => $isLocked ? 'locked' : $sc->status,
                        'is_premium_field' => (bool) $sc->is_premium_field,
                        'is_locked' => $isLocked,
                    ];
                });
            }),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function resolveLogoUrl(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        $url = url('storage/'.ltrim(str_replace('storage/', '', $this->logo), '/'));
        $version = $this->updated_at?->timestamp;

        return $version ? "{$url}?v={$version}" : $url;
    }

    private function resolveIsSaved(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (array_key_exists('is_saved', $this->resource->getAttributes())) {
            return (bool) $this->resource->getAttribute('is_saved');
        }

        if ($this->resource->relationLoaded('savedBy')) {
            return $this->savedBy->contains('user_id', $user->id);
        }

        return $this->savedBy()->where('user_id', $user->id)->exists();
    }
}
