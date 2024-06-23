<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppUserResource extends JsonResource
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

        return array_merge(parent::toArray($request), [

                "image"=> asset('uploads/user/' . $this->image),

            ]);
    }
}

