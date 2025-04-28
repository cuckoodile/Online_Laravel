<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductTransaction;
class Product extends Model
{
    protected $fillable = [
        "name",
        "price",
        "description",
        "category_id"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class)->withPivot('quantity','price');
    }
}
