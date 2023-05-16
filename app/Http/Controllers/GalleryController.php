<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Gallery;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;
use DB;
class GalleryController extends Controller
{
    public function index()
    {
        $data = [];
        try{
            $galleries = Gallery::where('deleted_at',null)->where('source','media')->get(['id', 'name','description', 'event_name', 'album_name', 'type', 'source']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($galleries); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($galleries[$i]['name'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $galleries[$i]['id'],"name" => $temporarySignedUrl, "description"=>$galleries[$i]['description'], "type" => $galleries[$i]['type'], "source" => $galleries[$i]['source'], "album_name" => $galleries[$i]['album_name'], "event_name" => $galleries[$i]['event_name'],];                        
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
            $galleries = Gallery::where('deleted_at',null)
                        ->where('id', $id)
                        ->get(['id', 'name','description', 'event_name', 'album_name', 'type', 'source']);
            // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
            $media = [];
            for($i = 0; $i < sizeof($galleries); $i++){
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($galleries[$i]['name'], now()->addMinutes(10));
                //print($temporarySignedUrl);

                $media[] = ["id" => $galleries[$i]['id'],"name" => $temporarySignedUrl, "description"=>$galleries[$i]['description'], "type" => $galleries[$i]['type'], "source" => $galleries[$i]['source'], "album_name" => $galleries[$i]['album_name'], "event_name" => $galleries[$i]['event_name'],];                        
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
