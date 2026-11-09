<?php

namespace App\Http\Resources;

use App\Support\PremiumAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subscription_tier' => $this->subscription_tier,
            'is_premium' => PremiumAccess::isPremium($this->resource),
            'premium_expires_at' => $this->premium_expires_at?->toIso8601String(),
            'avatar' => $this->avatar,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
