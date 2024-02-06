<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'features' => json_decode($this->features, true),
            'additional_features' => json_decode($this->additional_features, true),
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
