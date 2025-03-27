<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Models\TransactionPaymentMethod;
use App\Models\User;
use App\Models\ProductTransaction;


class Transaction extends Model
{
    public $fillable = [
        "user_id",
        "payment_method_id",
        "type_id",
        "status_id",
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }
    public function transaction_types() {
        return $this->belongsTo(TransactionType::class,"type_id","id");
    }
    
    public function transaction_statuses() {
        return $this->belongsTo(TransactionStatus::class,"status_id","id");
    }

    public function transaction_payment_methods() {
        return $this->belongsTo(TransactionPaymentMethod::class,"payment_method_id","id");
    }
    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('quantity','price');
    }
}
