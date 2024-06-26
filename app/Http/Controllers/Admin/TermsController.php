<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TermsResource;
use Illuminate\Support\Facades\Validator;

class TermsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $terms = Term::paginate($request->get('per_page', 50));
        return TermsResource::collection($terms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
     $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $term = new Term();
        $term->description = $request->description;
        $term->save();

        return response()->json(['isSuccess' => true,'data'=>new TermsResource( $term)], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Term $term)
    {
        return  new TermsResource( $term);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Term $term)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
       $term->description = $request->description;
       $term->save();

        return response()->json(['isSuccess' => true,'data'=>new TermsResource( $term)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        if($term){
            $term->delete();
            return response()->json(['isSuccess' => true], 200);
        }
        return response()->json(['error' => 'no found'],403);

    }
}
