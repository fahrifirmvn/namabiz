<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'rating',
        'quality_rating',
        'delivery_rating',
        'service_rating',
        'title',
        'comment',
        'reviewer_company',
        'reviewer_role',
        'is_verified_buyer',
        'supplier_reply',
        'replied_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'quality_rating' => 'integer',
        'delivery_rating' => 'integer',
        'service_rating' => 'integer',
        'is_verified_buyer' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
