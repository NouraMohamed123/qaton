<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResourceAccess extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


        public function toArray(Request $request): array
        {
            return [
                'access_images' => $this->AccessImages ? $this->AccessImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'apartment_id' => $image->apartment_id,
                        'image' => asset('uploads/apartments-access/' . $image->image),
                        'created_at' => $image->created_at,
                        'updated_at' => $image->updated_at,
                    ];
                }) : [],
             'website_link'=>$this->website_link,
             'login_instructions'=>$this->login_instructions,
             'internet_name'=>$this->internet_name,
             'internet_password'=>$this->internet_password,
             'instructions_prohibitions'=>$this->instructions_prohibitions,
             'apartment_features'=>$this->apartment_features,
             'contact_numbers'=>json_decode($this->contact_numbers),
             'secret_door'=> $this->secret_door,
             'access_video' => $this->access_video ? asset('uploads/access_video/' . $this->access_video) : null,

            ];
        }


}
