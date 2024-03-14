<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InformationController extends Controller
{
    public function index(Request $request)
    {
        $information = Information::paginate($request->get('per_page', 50));
        return response()->json(['Information'=>$information],200);
    }

    public function store(Request $request)
    {
        $information = Information::create($request->all());
        return response()->json(['Information'=>$information],200);
    }

    public function show($id)
    {
        $information = Information::find($id);
    
        if (!$information) {
            return response()->json(['message' => 'Information not found'], Response::HTTP_NOT_FOUND);
        }
    
        return response()->json($information, Response::HTTP_OK);
    }
    

    public function update(Request $request, Information $information)
    {
        $information->update($request->all());
        return response()->json($information);
    }

    public function destroy(Information $information)
    {
        if (!$information) {
            return response()->json(['message' => 'information not found'], 404);
        }
    
        $information->delete();
        
        return response()->json(['message' => 'information deleted successfully'], 200);
    }
}
