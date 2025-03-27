<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class TransactionType extends Model
{
    protected $fillable = [
        "name"
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }
}
