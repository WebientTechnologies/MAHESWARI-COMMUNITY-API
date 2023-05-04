<?php

namespace App\Http\Controllers;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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


           
            $data['status'] = "Success";
            $data['data'] = $newses;

            return response()->json($data, 200);
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
             
            $data['status'] = "Success";
            $data['data'] = $newses;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

}
