<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'house_address',
        'region',
        'province',
<<<<<<< HEAD
        'district',
        'city_municipality',
        'barangay',
<<<<<<< HEAD
        'subdivision_or_village', // (if applicable) // add |sometimes|'
        'street_number',
        'street_name',
        'unit_number',  // (if applicable) // add |sometimes|'
=======
        'city',
        'baranggay', 
>>>>>>> 69bff22 (Product Comments)
=======
        'subdivision_village',
        'street',
        'block_number',
        'lot_number',  
>>>>>>> c88078fc465a0c6707a08714657eabbb89d86fbf
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