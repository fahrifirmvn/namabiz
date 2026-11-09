<?php

namespace App\Models;

use Database\Factories\ComparisonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    /** @use HasFactory<ComparisonFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'supplier_ids'];

    protected $casts = [
        'supplier_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
