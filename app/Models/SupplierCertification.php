<?php

namespace App\Models;

use App\Observers\CatalogCacheObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CatalogCacheObserver::class)]
class SupplierCertification extends Model
{
    public $timestamps = false;

    protected $fillable = ['supplier_id', 'certification_id', 'status', 'is_premium_field'];

    protected $casts = [
        'is_premium_field' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }
}
