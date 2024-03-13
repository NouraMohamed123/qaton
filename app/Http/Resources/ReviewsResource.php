<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'user_phone' => $this->user->phone,
            'apartment_id' => $this->apartment_id,
            'descriptions' => $this->descriptions,
            'rating' => $this->rating,
            'comfort_rating' => $this->comfort_rating,
            'location_rating' => $this->location_rating,
            'facilities_rating' => $this->facilities_rating,
            'cleanliness_rating' => $this->cleanliness_rating,
            'staff_rating' => $this->staff_rating,
            'liked' => $this->liked,
            'disliked' => $this->disliked,
            'describe_stay' => $this->describe_stay,
           
        ];
    }
}
