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
        'city_or_municipality',
        'barangay',
        'subdivision_or_village', // (if applicable) // add |sometimes|'
        'street_number',
        'street_name',
        'unit_number',  // (if applicable) // add |sometimes|'
        'zip_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
