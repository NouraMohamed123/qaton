<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return  [
    'date_from'=>$this->date_from,
    'date_to'=>$this->date_to,
    'price'=>$this->total_price,
    'status'=>$this->status,
    'leaving' =>$this->exit,
        'customer'=>[
            'name' =>$this->user->name,
            'email' =>$this->user->email,
            'avatar' => asset('uploads/user/' .$this->user->avatar),
        ],
        'apartment' =>[
         'name' =>$this->apartment->name,
         'images' => $this->apartment->images->map(function ($image) {
            return [
                asset('uploads/apartments/' . $image->image),
            ];
        })->flatten()->toArray(),
        'prices' => $this->apartment->prices->map(function ($price) {
            return [
                'price' => $price->price,
                'date' => $price->date,

            ];
        }),
    ],

    ];
}

}
