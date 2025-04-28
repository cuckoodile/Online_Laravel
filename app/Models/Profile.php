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
<<<<<<< HEAD
=======
        "isAdmin",
        "user_id",
        
>>>>>>> 69bff22 (Product Comments)
=======
        
>>>>>>> c88078fc465a0c6707a08714657eabbb89d86fbf
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
