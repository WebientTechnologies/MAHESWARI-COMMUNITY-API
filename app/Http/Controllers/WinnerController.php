<?php

namespace App\Http\Controllers;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use DB;
class WinnerController extends Controller
{
    public function index(){
        
        $data = [];
        try{
            $winners = DB::table('winners')
            ->where('winners.deleted_at', null)
            ->orderBy('id','DESC')
            ->get(['winners.id',
            'winners.quiz_id',
            'winners.first_winner',
            'winners.second_winner',
            'winners.third_winner',
            'winners.created_at',
            'winners.updated_at',
            'winners.deleted_at',
            ]);


           
            $data['status'] = "Success";
            $data['data'] = $winners;

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
            $winners = DB::table('winners')
            ->where('id', '=', $id)
            ->get(['winners.id',
            'winners.quiz_id',
            'winners.first_winner',
            'winners.second_winner',
            'winners.third_winner',
            'winners.created_at',
            'winners.updated_at',
            'winners.deleted_at',
            ]);
             
            $data['status'] = "Success";
            $data['data'] = $winners;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }
}
