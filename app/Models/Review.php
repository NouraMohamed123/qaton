<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function user()
    {
        return $this->belongsTo(AppUsers::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    protected $casts = [
        'apartment_id'=>'integer',
        'user_id'=>'integer',
        'comfort_rating'=>'integer',
        'location_rating'=>'integer',
        'facilities_rating'=>'integer',
        'cleanliness_rating'=>'integer',
        'staff_rating'=>'integer',
        'rating'=>'double',

    ];
}
