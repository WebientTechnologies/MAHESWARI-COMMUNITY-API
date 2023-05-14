<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;
use DB;
class PromotionController extends Controller
{
    public function index()
    {
        $data = [];
        try{
            $promotions = Promotion::where('deleted_at',null)->get(['id', 'file','start_date', 'end_date', 'link', 'created_at', 'updated_at']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($promotions); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($promotions[$i]['file'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $promotions[$i]['id'],"file" => $temporarySignedUrl, "start_date"=>$promotions[$i]['start_date'], "end_date" => $promotions[$i]['end_date'], "link" => $promotions[$i]['link'], "created_at" => $promotions[$i]['created_at'], "updated_at" => $promotions[$i]['updated_at'],];                        
            }
        
            $media = collect($media);
           
            $data['status'] = "Success";
            $data['data'] = $media;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }

    }

    public function show($id)
    {
        $data = [];
        try{
            $promotions = Promotion::where('deleted_at',null)
                        ->where('id', $id)
                        ->get(['id', 'file','start_date', 'end_date', 'link', 'created_at', 'updated_at']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($promotions); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($promotions[$i]['file'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $promotions[$i]['id'],"file" => $temporarySignedUrl, "start_date"=>$promotions[$i]['start_date'], "end_date" => $promotions[$i]['end_date'], "link" => $promotions[$i]['link'], "created_at" => $promotions[$i]['created_at'], "updated_at" => $promotions[$i]['updated_at'],];                        
            }
        
        
            $media = collect($media);
           
            $data['status'] = "Success";
            $data['data'] = $media;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }

    }
}