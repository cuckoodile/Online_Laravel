<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Models\TransactionPaymentMethod;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Address;


class Transaction extends Model
{
    public $fillable = [
        // Auto Filled Based on the Logged User
        "user_id",
        "address_id",

        "status_id",
        "payment_method_id",
        "type_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function address() {
        return $this->belongsTo(Address::class);
    }
    public function transaction_statuses() {
        return $this->belongsTo(TransactionStatus::class,"status_id","id");
    }
    public function transaction_payment_methods() {
        return $this->belongsTo(TransactionPaymentMethod::class,"payment_method_id","id");
    }

    public function transaction_types() {
        return $this->belongsTo(TransactionType::class,"type_id","id");
    }

    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('quantity','total_price');
    }
}
