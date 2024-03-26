<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;
    protected $guarded =['id'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorit::class, 'apartment_id', 'id');
    }
    public function BookedApartments()
    {
        return $this->hasMany(Booked_apartment::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function AccessImages()
    {
        return $this->hasMany(AccessImage::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function prices()
    {
        return $this->hasMany(price::class);
    }
    protected $casts = [
        'unit_space'=>'integer',
        'price'=>'double',
        'bathrooms' => 'integer',
        'lounges' => 'integer',
        'dining_session' => 'integer',
        'area_id' => 'integer',
        'max_guests' => 'integer',
        'max_rooms' => 'integer',
        'status' => 'integer',
        'available' => 'integer',
        'beds_childs' => 'integer',
    ];
}
