<?php

namespace App\Http\Controllers;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use DB;
class WinnerController extends Controller
{
    public function index(){
        
        $data = [];
        try{
            $winners = DB::table('winners')
            ->where('winners.deleted_at', null)
            ->leftjoin('quizzes as qz', 'winners.quiz_id', '=', 'qz.id')
            ->orderBy('id','DESC')
            ->get(['winners.id',
            'qz.id As quiz_id',
            'qz.title As quiz_name',
            'qz.file As file',
            'winners.first_winner',
            'winners.second_winner',
            'winners.third_winner',
            'winners.created_at',
            'winners.updated_at',
            'winners.deleted_at',
            ]);

            $media = [];
            foreach ($winners as $winner) {
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($winner->file, now()->addMinutes(10));
                

                $media[] = ["file" => $temporarySignedUrl];
            }
            $mergedWinners = $winners->map(function ($item, $key) use ($media) {
                $item->media = $media[$key];
                return $item;
            });
           
            $data['status'] = "Success";
            $data['data'] = $mergedWinners;

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
            ->where('winners.id', '=', $id)         
            ->leftjoin('quizzes as qz', 'winners.quiz_id', '=', 'qz.id')
            ->get(['winners.id',
            'qz.id As quiz_id',
            'qz.title As quiz_name',
            'qz.file As file',
            'winners.first_winner',
            'winners.second_winner',
            'winners.third_winner',
            'winners.created_at',
            'winners.updated_at',
            'winners.deleted_at',
            ]);
            
            $media = [];
            foreach ($winners as $winner) {
                $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($winner->file, now()->addMinutes(10));
                

                $media[] = ["file" => $temporarySignedUrl];
            }
            $mergedWinners = $winners->map(function ($item, $key) use ($media) {
                $item->media = $media[$key];
                return $item;
            });
           
            $data['status'] = "Success";
            $data['data'] = $mergedWinners;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }
}
