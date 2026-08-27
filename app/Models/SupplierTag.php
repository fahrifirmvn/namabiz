<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierTag extends Model
{
    public $timestamps = false;

    protected $fillable = ['supplier_id', 'tag_name'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
