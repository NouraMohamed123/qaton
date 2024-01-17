<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUsers extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'api_token',
        'image',
        'email',
        'phone',
        'address',
        'city_id',
        
        'otp',
    ];

    protected $hidden = [
        'password',
    ];
}
