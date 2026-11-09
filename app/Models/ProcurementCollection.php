<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProcurementCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedSuppliers(): HasMany
    {
        return $this->hasMany(SavedSupplier::class, 'collection_id');
    }

    public function suppliers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Supplier::class,
            SavedSupplier::class,
            'collection_id',
            'id',
            'id',
            'supplier_id'
        );
    }
}
