<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewsResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'message' => 'Reviews retrieved successfully',
            'data' => ReviewsResource::collection($reviews),
        ], Response::HTTP_OK);
    }


    public function show($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found for the given Apartment ID'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Review retrieved successfully',
            'data' => $review,
        ], Response::HTTP_OK);
    }
    public function show_review($apartment_id)
    {

        $review = Review::where('apartment_id', $apartment_id)->get();

        if (!$review) {
            return response()->json(['message' => 'Review not found for the given Apartment ID'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Review retrieved successfully',
            'data' => $review,
        ], Response::HTTP_OK);
    }


    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $review->delete();
            return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_OK);
    }
}
