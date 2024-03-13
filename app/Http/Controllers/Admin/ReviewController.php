<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Reviews retrieved successfully',
            'data' => ReviewResource::collection($reviews),
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Review retrieved successfully',
            'data' => new ReviewResource($review),
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
