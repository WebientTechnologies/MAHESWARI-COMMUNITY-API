<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qualification;
use Illuminate\Http\JsonResponse;


class QualificationController extends Controller
{
    public function index(){
        $qual = Qualification::get();
        return response()->json($qual,200);
    }

    public function store(Request $request){

        $qual = new Qualification();
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
