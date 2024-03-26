<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::paginate(10); 
        return response()->json($discounts, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|numeric',
            'type' => 'required|in:monthly,weekly',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $discount = Discount::create($request->all());

        return response()->json($discount, 201);
    }

    public function show(Discount $discount)
    {
        return response()->json($discount, 200);
    }

    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|numeric',
            'type' => 'required|in:monthly,weekly',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $discount->update($request->all());

        return response()->json($discount, 200);
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return response()->json(null, 204);
    }
}
