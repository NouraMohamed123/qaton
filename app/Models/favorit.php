<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    protected $guarded =[];
    public function user()
    {
        return $this->belongsTo(AppUsers::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
