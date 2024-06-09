<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return  [
        'name'=>$this->date_from,
        'invoice_id'=>$this->invoice_id,
        'invoice_url'=>$this->invoice_url,
        'invoice_status'=>$this->invoice_status,
        'customer'=>[
            'name' =>$this->booked->user->name,
            'email' =>$this->booked->user->email,
            'avatar' => asset('uploads/user/' .$this->booked->user->avatar),
        ],
       'booked'=>[
            'date_from' =>$this->booked->date_from,
            'date_to' =>$this->booked->date_to,
            'status' =>$this->booked->status,
            'leaving' =>$this->booked->exit,
            'coupon' =>$this->booked->coupon_id == 1?$this->booked->coupon->discount_code:0,
            'apartment' =>$this->booked->apartment->name,

        ],
    ];
}

}
