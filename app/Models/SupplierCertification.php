<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
