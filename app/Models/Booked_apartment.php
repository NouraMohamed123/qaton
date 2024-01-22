<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booked_apartment extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function Apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
