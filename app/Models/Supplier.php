<?php

namespace App\Models;

use App\Observers\CatalogCacheObserver;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CatalogCacheObserver::class)]
class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'logo', 'tagline', 'description', 'country', 'city',
        'industry_category', 'is_verified', 'about', 'website', 'whatsapp', 'email',
        'founded_year', 'company_size', 'production_capacity', 'moq',
        'avg_lead_time', 'carbon_footprint_report',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'founded_year' => 'integer',
    ];

    /**
     * Load only the aggregates needed by supplier cards instead of whole relations.
     */
    public function scopeWithCardStats(Builder $query): Builder
    {
        return $query->withCount(['documents', 'reviews'])->withAvg('reviews', 'rating');
    }

    /**
     * Score how complete the public profile is.
     *
     * @return array{percent: int, missing: array<int, string>}
     */
    public function profileCompleteness(): array
    {
        $count = function (string $relation): int {
            if (array_key_exists($relation.'_count', $this->attributes)) {
                return (int) $this->attributes[$relation.'_count'];
            }

            if ($this->relationLoaded($relation)) {
                return $this->getRelation($relation)->count();
            }

            return $this->{$relation}()->count();
        };

        $checks = [
            'Logo' => filled($this->logo),
            'Tagline' => filled($this->tagline),
            'Deskripsi / Tentang' => filled($this->about) || filled($this->description),
            'Kontak WhatsApp atau Email' => filled($this->whatsapp) || filled($this->email),
            'Website' => filled($this->website),
            'Kota' => filled($this->city),
            'MOQ' => filled($this->moq),
            'Kapasitas produksi' => filled($this->production_capacity),
            'Lead time' => filled($this->avg_lead_time),
            'Ukuran perusahaan' => filled($this->company_size),
            'Tahun berdiri' => filled($this->founded_year),
            'Kemampuan inti (tag)' => $count('tags') > 0,
            'Sertifikasi' => $count('certifications') > 0,
            'Dokumen' => $count('documents') > 0,
        ];

        $missing = array_keys(array_filter($checks, fn (bool $done): bool => ! $done));

        return [
            'percent' => (int) round((count($checks) - count($missing)) / count($checks) * 100),
            'missing' => $missing,
        ];
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (empty($this->whatsapp)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $text = urlencode("Halo {$this->name}, saya menemukan profil perusahaan Anda di platform Namabiz.");

        return "https://wa.me/{$phone}?text={$text}";
    }

    public function tags()
    {
        return $this->hasMany(SupplierTag::class);
    }

    public function certifications()
    {
        return $this->hasMany(SupplierCertification::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function savedBy()
    {
        return $this->hasMany(SavedSupplier::class);
    }

    public function reviews()
    {
        return $this->hasMany(SupplierReview::class);
    }

    /**
     * Aggregate rating statistics without loading review rows.
     *
     * @return array{count: int, average: ?float, quality: ?float, delivery: ?float, service: ?float, breakdown: array<int, int>}
     */
    public function reviewStats(): array
    {
        $totals = $this->reviews()
            ->selectRaw('count(*) as total, avg(rating) as avg_rating, avg(quality_rating) as avg_quality, avg(delivery_rating) as avg_delivery, avg(service_rating) as avg_service')
            ->toBase()
            ->first();

        $perStar = $this->reviews()
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->toBase()
            ->pluck('total', 'rating');

        $count = (int) ($totals->total ?? 0);
        $breakdown = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $breakdown[$star] = $count > 0 ? (int) round(((int) ($perStar[$star] ?? 0) / $count) * 100) : 0;
        }

        $round = fn ($value): ?float => $count > 0 ? round((float) $value, 1) : null;

        return [
            'count' => $count,
            'average' => $round($totals->avg_rating ?? 0),
            'quality' => $round($totals->avg_quality ?? 0),
            'delivery' => $round($totals->avg_delivery ?? 0),
            'service' => $round($totals->avg_service ?? 0),
            'breakdown' => $breakdown,
        ];
    }

    public function getAverageRatingAttribute(): ?float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            $avg = $this->attributes['reviews_avg_rating'];
        } elseif ($this->relationLoaded('reviews')) {
            $avg = $this->reviews->avg('rating');
        } else {
            $avg = $this->reviews()->avg('rating');
        }

        return $avg ? round((float) $avg, 1) : null;
    }

    public function ratingBreakdown(): array
    {
        $reviews = $this->relationLoaded('reviews') ? $this->reviews : $this->reviews()->get();
        $total = $reviews->count();

        $breakdown = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        if ($total === 0) {
            return $breakdown;
        }

        foreach ($reviews as $rev) {
            if (isset($breakdown[$rev->rating])) {
                $breakdown[$rev->rating]++;
            }
        }

        return array_map(fn ($count) => round(($count / $total) * 100), $breakdown);
    }
}
