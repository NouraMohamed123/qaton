<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookedResourceMobile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return array_merge(parent::toArray($request), [
        'apartment' => new ApartmentResource($this->apartment),
        'user_name'=>$this->user->name
    ]);
}

}
