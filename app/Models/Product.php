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
        return $this->belongsToMany(Transaction::class, 'product_transaction')
                    ->withPivot('quantity', 'price', 'sub_total');
    }
    public function product_comments()
    {
        return $this->hasMany(ProductComment::class);
    }
    public function product_specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function getStockAttribute()
    {
        $inboundStock = $this->transactions()
            ->where('type_id', 1)
            ->sum('product_transaction.quantity');

        $outboundStock = $this->transactions()
            ->where('type_id', 2)
            ->sum('product_transaction.quantity');

        return $inboundStock - $outboundStock;
    }
}
