<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Degree;
use Illuminate\Http\JsonResponse;

class DegreeController extends Controller
{
    public function index(){
        $qual = Degree::get();
        return response()->json($qual,200);
    }

    public function store(Request $request){

        $qual = new Degree();
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
