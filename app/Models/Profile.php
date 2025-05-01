<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        "profile_image",
        "first_name",
        "last_name",
        "contact_number",
        "is_admin",
        "user_id",
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
