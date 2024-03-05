<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualNotification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'date', 'message'];

    public function notifiable()
    {
        return $this->morphTo();
    }

}
