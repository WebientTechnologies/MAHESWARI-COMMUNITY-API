<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use DB;

class EventController extends Controller
{
    public function index(){
        
        $data = [];
        try{
            $events = DB::table('events')
            ->where('events.deleted_at', null)
            ->leftJoin('users as ucb', 'events.created_by', '=', 'ucb.id')
            ->leftJoin('users as uub', 'events.updated_by', '=', 'uub.id')
            ->leftJoin('users as udb', 'events.deleted_by', '=', 'udb.id')
            ->orderBy('id','DESC')
            ->get(['events.id',
            'events.title',
            'events.description',
            'events.event_start_at',
            'events.event_end_at',
            'ucb.id AS created_by_uid',
            'ucb.name AS created_by_name',
            'uub.id AS updated_by_uid',
            'uub.name AS updated_by_name',
            'udb.id AS deleted_by_uid',
            'udb.name AS deleted_by_name',
            'events.created_at',
            'events.updated_at',
            'events.deleted_at',
            ]);


           
            $data['status'] = "Success";
            $data['data'] = $events;

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
            $events = DB::table('events')
            ->where('events.id', '=', $id)
            ->leftJoin('users as ucb', 'events.created_by', '=', 'ucb.id')
            ->leftJoin('users as uub', 'events.updated_by', '=', 'uub.id')
            ->leftJoin('users as udb', 'events.deleted_by', '=', 'udb.id')
            ->get(['events.id',
            'events.title',
            'events.description',
            'events.event_start_at',
            'events.event_end_at',
            'ucb.id AS created_by_uid',
            'ucb.name AS created_by_name',
            'uub.id AS updated_by_uid',
            'uub.name AS updated_by_name',
            'udb.id AS deleted_by_uid',
            'udb.name AS deleted_by_name',
            'events.created_at',
            'events.updated_at',
            'events.deleted_at',
            ]);
             
            $data['status'] = "Success";
            $data['data'] = $events;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }
}
