<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        "first_name",
        "last_name",
        "contact_number",
<<<<<<< HEAD
=======
        "isAdmin",
        "user_id",
        
>>>>>>> 69bff22 (Product Comments)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
