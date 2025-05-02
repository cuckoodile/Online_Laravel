<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "name",
        "price",
        "admin_id",
        "product_image",
        "description",
        "stock",
        "category_id"
    ];

    protected $casts = [
        'product_image' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class)->withPivot('quantity','total_price');
    }
    public function product_comments()
    {
        return $this->hasMany(ProductComment::class);
    }
    public function product_specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }
}
