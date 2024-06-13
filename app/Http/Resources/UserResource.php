<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function with($request)
    {
        return [
            'status' => 'true',
            'message' => 'تمت العميله بنجاح'
        ];
    }


    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'name' => $this->name??null,
            'email' => $this->email??null,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth??null,
            'national_id' => $this->national_id??null,
            'photo' => asset('uploads/user/' . $this->photo),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles_name' => $this->roles->first()?->name ?? null,      ];
    }
}

