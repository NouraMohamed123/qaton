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

        $ratingSum = $this->reviews->sum('rating');
        $bedsSum = $this->rooms->sum('beds');
        $area_name = $this->area->name;

        return array_merge(parent::toArray($request), [
            'features' => json_decode($this->features),
            'additional_features' => json_decode($this->additional_features),
            'contact_numbers' => json_decode($this->contact_numbers),
            'tax' => $taxAddedValue,
            'total_price' => $total_price,
            'total_price_nights' => ($this->nights) * $total_price,
            'city_id'=>$this->area->city->id,
            'area_name' =>   $area_name,
            'rating' => $ratingSum,
            'beds' => $bedsSum,
            'reviews' => $this->reviews->map(function ($review) {
                return array_merge($review->toArray(), [
                    'user_name' => $review->user->name,
                ]);
            }),
            'images' => $this->images->map(function ($image) {
                return [
                    asset('uploads/apartments/' . $image->image),
                ];
            })->flatten()->toArray(),
            'access_images' => $this->AccessImages ? $this->AccessImages->map(function ($image) {
                return [
                    asset('uploads/apartments-access/' . $image->image),
                ];
            })->flatten()->toArray() : [],
            'prices' => $this->prices->map(function ($price) {
                return [
                    'price' => $price->price,
                    'date' => $price->date,

                ];
            }),
        ]);
    }
}
