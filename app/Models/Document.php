<?php

namespace App\Models;

use App\Observers\CatalogCacheObserver;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CatalogCacheObserver::class)]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    protected $fillable = ['supplier_id', 'title', 'type', 'file_path', 'external_url', 'is_premium'];

    protected $casts = [
        'is_premium' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
