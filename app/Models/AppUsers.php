<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Traits\JWTSubject as JWTSubjectTrait;

class AppUsers extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles , HasPermissions  ;



    protected $fillable = [

        'id',
        'name',
        'password',
        'api_token',
        'image',
        'email',
        'phone',
        'address',
        'city_id',
        'otp',
        'type'
    ];

    protected $hidden = [
        'password',
    ];

  public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

