<?php

namespace App\Http\Controllers;
use App\Models\News;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use DB;

class NewsController extends Controller
{
    public function index(){
        
        $data = [];
        try{
            $newses = DB::table('newses')
            ->where('newses.deleted_at', null)
            ->leftJoin('users as ucb', 'newses.created_by', '=', 'ucb.id')
            ->leftJoin('users as uub', 'newses.updated_by', '=', 'uub.id')
            ->leftJoin('users as udb', 'newses.deleted_by', '=', 'udb.id')
            ->orderBy('id','DESC')
            ->get(['newses.id',
            'newses.title',
            'newses.description',
            'ucb.id AS created_by_uid',
            'ucb.name AS created_by_name',
            'uub.id AS updated_by_uid',
            'uub.name AS updated_by_name',
            'udb.id AS deleted_by_uid',
            'udb.name AS deleted_by_name',
            'newses.created_at',
            'newses.updated_at',
            'newses.deleted_at',
            ]);

            $totalGroup = count($newses);
            $perPage = 15;
            $page = Paginator::resolveCurrentPage('page');
        
            $newses = new LengthAwarePaginator($newses->forPage($page, $perPage), $totalGroup, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

            $u1 = json_encode($newses,true);
            $u2 = json_decode($u1,true);

            $current_page = $u2['current_page'];
            $first_page_url = $u2['first_page_url'];
            $from = $u2['from'];
            $last_page = $u2['last_page'];
            $last_page_url = $u2['last_page_url'];
            $links = $u2['links'];
            $next_page_url = $u2['next_page_url'];
            $path = $u2['path'];
            $per_page = $u2['per_page'];
            $prev_page_url = $u2['prev_page_url'];
            $to = $u2['to'];
            $total = $u2['total'];

            $u3 = json_encode($u2['data'],true);
            $u4 = json_decode($u3,true);
           
            

            $main = [];
            $master = array();
            foreach($u4 as $kk => $vv){
                $id = 0;
                
                foreach($u4[$kk] as $key => $v ){
                    if($key == 'id'){
                        $id = $v;
                    }
                    $master[$key] = $v;
                }
                
                $galleries = Gallery::where('deleted_at',null)
                                ->where('news_id', $id)
                                ->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
                // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
                $media = [];
                for($i = 0; $i < sizeof($galleries); $i++){
                    $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($galleries[$i]['name'], now()->addMinutes(10));
                    //print($temporarySignedUrl);

                    $media[] = ["id" => $galleries[$i]['id'],"name" => $temporarySignedUrl, "type" => $galleries[$i]['type'], "source" => $galleries[$i]['source'], "album_name" => $galleries[$i]['album_name'], "event_name" => $galleries[$i]['event_name'],];                        
                }
       
                 $media = collect($media);

                $master['media'] = $media;
                $main[] = $master;

            }
            $data['current_page'] = $current_page;
            $data['data'] = $main;
            $data['first_page_url'] = $first_page_url;
            $data['from'] = $from;
            $data['last_page'] = $last_page;
            $data['last_page_url'] = $last_page_url;
            $data['links'] = $links;
            $data['next_page_url'] = $next_page_url;
            $data['path'] = $path;
            $data['per_page'] = $per_page;
            $data['prev_page_url'] = $prev_page_url;
            $data['to'] = $to;
            $data['total'] = $total;
           
            $mdata['status'] = "Success"; 
            $mdata['data'] = $data; 

            return response()->json($mdata, 200);

        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }


    public function show($id){
        $data = [];
        try{
            $newses = DB::table('newses')
            ->where('newses.id', '=', $id)
            ->leftJoin('users as ucb', 'newses.created_by', '=', 'ucb.id')
            ->leftJoin('users as uub', 'newses.updated_by', '=', 'uub.id')
            ->leftJoin('users as udb', 'newses.deleted_by', '=', 'udb.id')
            ->get(['newses.id',
            'newses.title',
            'newses.description',
            'ucb.id AS created_by_uid',
            'ucb.name AS created_by_name',
            'uub.id AS updated_by_uid',
            'uub.name AS updated_by_name',
            'udb.id AS deleted_by_uid',
            'udb.name AS deleted_by_name',
            'newses.created_at',
            'newses.updated_at',
            'newses.deleted_at',
            ]);
            if(!empty($newses)){
                $galleries = Gallery::where('deleted_at',null)
                                ->where('news_id', $id)
                                ->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
                // $media = Gallery::where('deleted_at',null)->get(['id', 'name', 'event_name', 'album_name', 'type', 'source']);
                $media = [];
                for($i = 0; $i < sizeof($galleries); $i++){
                    $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($galleries[$i]['name'], now()->addMinutes(10));
                    //print($temporarySignedUrl);

                    $media[] = ["id" => $galleries[$i]['id'],"name" => $temporarySignedUrl, "type" => $galleries[$i]['type'], "source" => $galleries[$i]['source'], "album_name" => $galleries[$i]['album_name'], "event_name" => $galleries[$i]['event_name'],];                        
                }
       
                 $media = collect($media);
                
                $newses['media'] = $media;
                $data['status'] = "Success";
                $data['data'] = $newses;
    
                return response()->json($data, 200);
            }else{
                
                $data['message'] = "No Record Found";
    
                return response()->json($data, 409);
                
            }
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

}
