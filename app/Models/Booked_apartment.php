<?php

namespace App\Models;

use Carbon\Carbon;
use App\Notifications\UserLogin;
use App\Notifications\UserLogout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booked_apartment extends Model
{

    use HasFactory, Notifiable;
    protected $guarded =[];
    public function user()
    {
        return $this->belongsTo(AppUsers::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function getStatusAttribute()
    {
        $dateTo = Carbon::parse($this->date_to)->toDateString();
        $now = Carbon::now()->toDateString();
        if ($this->attributes['status'] === 'canceled') {
            return 'canceled';
        }

        if ($dateTo < $now) {
            return 'past';
        }
        return $this->attributes['status'];
    }
    // public function sendDateFromNotification()
    // {
    //     $notificationDate = Carbon::parse($this->date_from)->addMinutes(3); // Add 3 minutes to the date_from value
    //     $user = AppUsers::find($this->user_id);
    //     $user->notify((new UserLogin($user))->delay($notificationDate));

    // }
    // public function sendDateToNotification()
    // {
    //     $notificationDate = Carbon::parse($this->date_to);
    //     $user = AppUsers::find($this->user_id);

    //     Notification::send($user, (new UserLogout()));

    // }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
