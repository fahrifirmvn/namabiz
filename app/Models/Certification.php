<?php

namespace App\Models;

use Database\Factories\CertificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
