<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaritalStatus;
use Illuminate\Http\JsonResponse;

class MaritalStatusController extends Controller
{
    public function index(){
        $qual = MaritalStatus::get();
        return response()->json($qual,200);
    }

    public function store(Request $request){

        $qual = new MaritalStatus();
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
