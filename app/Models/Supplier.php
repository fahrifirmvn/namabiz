<?php

namespace App\Models;

use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getAverageRatingAttribute(): ?float
    {
        if ($this->relationLoaded('reviews')) {
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
