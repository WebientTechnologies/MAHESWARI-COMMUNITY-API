<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occupation;
use Illuminate\Http\JsonResponse;

class OccupationController extends Controller
{
    public function index(){
        $qual = Occupation::get();
        return response()->json($qual,200);
    }

    public function store(Request $request){

        $qual = new Occupation();
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
