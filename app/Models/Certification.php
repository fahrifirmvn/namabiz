<?php

namespace App\Models;

use App\Observers\CatalogCacheObserver;
use Database\Factories\CertificationFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CatalogCacheObserver::class)]
class Certification extends Model
{
    /** @use HasFactory<CertificationFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'category'];

    public function suppliers()
    {
        return $this->hasMany(SupplierCertification::class);
    }
}
