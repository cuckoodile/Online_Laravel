<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
   // since yung cart natin is private, kailangan i-link yung cart sa specific user
   protected $fillable = [
    'user_id',
    'product_id',
    'quantity',
    'total_price',
];


    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}