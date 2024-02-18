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
}
