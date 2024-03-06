<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'apartment_id' => 'required|exists:apartments,id',
            'descriptions' => 'nullable|string',
            'rating' => 'required|numeric|min:0|max:10',
            'comfort_rating' => 'required|numeric|min:0|max:10',
            'location_rating' => 'required|numeric|min:0|max:10',
            'facilities_rating' => 'required|numeric|min:0|max:10',
            'cleanliness_rating' => 'required|numeric|min:0|max:10',
            'staff_rating' => 'required|numeric|min:0|max:10',
            'liked' => 'nullable|string',
            'disliked' => 'nullable|string',
            'describe_stay' => 'nullable|string',
        ];
    }
}
