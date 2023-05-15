<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;
use DB;
class QuizController extends Controller
{
    public function index()
    {
        $data = [];
        try{
            $quizes = Quiz::where('deleted_at',null)->get(['id', 'title', 'description', 'file','start_time', 'end_time', 'link', 'created_at', 'updated_at']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($quizes); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($quizes[$i]['file'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $quizes[$i]['id'], "title" => $quizes[$i]['title'], "description" => $quizes[$i]['description'], "file" => $temporarySignedUrl, "start_date"=>$quizes[$i]['start_date'], "end_date" => $quizes[$i]['end_date'], "link" => $quizes[$i]['link'], "created_at" => $quizes[$i]['created_at'], "updated_at" => $quizes[$i]['updated_at'],];                        
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
            $quizes = Quiz::where('deleted_at',null)
                        ->where('id', $id)
                        ->get(['id', 'title', 'description', 'file','start_time', 'end_time', 'link', 'created_at', 'updated_at']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($quizes); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($quizes[$i]['file'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $quizes[$i]['id'], "title" => $quizes[$i]['title'], "description" => $quizes[$i]['description'], "file" => $temporarySignedUrl, "start_date"=>$quizes[$i]['start_date'], "end_date" => $quizes[$i]['end_date'], "link" => $quizes[$i]['link'], "created_at" => $quizes[$i]['created_at'], "updated_at" => $quizes[$i]['updated_at'],];                        
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

