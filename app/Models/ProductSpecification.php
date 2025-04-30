<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $fillable = [
        'product_id',
        'details',
    ];
    // Use casts helper that converts a data type to another
    // In this case, we are converting the details column to an array 
    protected $casts = [
        'details' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
