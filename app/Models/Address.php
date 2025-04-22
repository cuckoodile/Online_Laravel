<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'region',
        'province',
        'district',
        'city_municipality',
        'barangay',
        'subdivision_village',
        'street',
        'block_number',
        'lot_number',  
        'zip_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function transactions() 
    {
        return $this->hasMany(Transaction::class);
    }
}