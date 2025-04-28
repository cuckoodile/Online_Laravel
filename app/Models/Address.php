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
        'city_or_municipality',
        'barangay',
        'subdivision_or_village', // (if applicable) // add |sometimes|'
        'street_number',
        'street_name',
        'unit_number',  // (if applicable) // add |sometimes|'
=======
        'city',
        'baranggay', 
>>>>>>> 69bff22 (Product Comments)
        'zip_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
