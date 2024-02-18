<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $taxAddedValue = $settings['tax_added_value'];
        $total_price = $taxAddedValue ? $this->price + $taxAddedValue : $this->price;

        return array_merge(parent::toArray($request), [
            'features' => json_decode($this->features),
            'tax' =>   $taxAddedValue ,
            'total_price' => $total_price,
            'additional_features' => json_decode($this->additional_features),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'apartment_id' => $image->apartment_id,
                    'image' => asset('uploads/apartments/' . $image->image),
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ];
            }),
        ]);
    }
}
