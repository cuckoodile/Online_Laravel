<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class TransactionPaymentMethod extends Model
{
    protected $fillable = [
        "name"
    ];

    public function Transaction() {
        $this->hasMany(Transaction::class);
    }
}
