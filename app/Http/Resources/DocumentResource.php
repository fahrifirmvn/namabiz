<?php

namespace App\Http\Resources;

use App\Support\PremiumAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
        $canDownload = ! $this->is_premium || $isPremium;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'is_premium' => (bool) $this->is_premium,
            'can_download' => $canDownload,
            'download_url' => route('api.documents.download', ['document' => $this->id]),
            'external_url' => $this->external_url,
        ];
    }
}
