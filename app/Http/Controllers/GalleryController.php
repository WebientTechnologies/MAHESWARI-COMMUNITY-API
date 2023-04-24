<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Gallery;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use File;
use DB;
class GalleryController extends Controller
{
    public function index()
    {
        $data = [];
        try{
            $galleries = DB::table('galleries')
            ->orderBy('id','DESC')
            ->get();
           
            $data['status'] = "Success";
            $data['data'] = $galleries;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }

    }
}
