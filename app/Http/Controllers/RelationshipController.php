<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relationship;
use Illuminate\Http\JsonResponse;

class RelationshipController extends Controller
{
    public function index(){
        $qual = Relationship::get();
        return response()->json($qual,200);
    }

    public function store(Request $request){

        $qual = new Relationship();
        $qual->name = $request->name;

        $qual->save();

        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Successfully created.', 
            ], 
            201
        );
    }
}
