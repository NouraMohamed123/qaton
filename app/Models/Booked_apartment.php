<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booked_apartment extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function user()
    {
        return $this->belongsTo(AppUsers::class);
    }

    public function Apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function getStatusAttribute()
    {
        if ($this->date_from <= now()) {
            return 'past';
        }

        return $this->attributes['status'];
    }
}
