<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    protected $guarded =[];
    protected static function booted() {
        static::creating(function ($model) {
          $model->invoice_number =  mt_rand(100000000, 999999999);
     });
    }
    public function booked()
    {
        return $this->hasOne(Booked_apartment::class ,'booked_id');
    }
}
