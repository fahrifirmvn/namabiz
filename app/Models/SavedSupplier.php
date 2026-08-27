<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSupplier extends Model
{
    protected $fillable = ['user_id', 'supplier_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
