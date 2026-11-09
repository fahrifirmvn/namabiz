<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSupplier extends Model
{
    protected $fillable = ['user_id', 'supplier_id', 'collection_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ProcurementCollection::class, 'collection_id');
    }
}
