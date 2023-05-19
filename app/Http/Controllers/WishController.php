<?php
namespace App\Http\Controllers;
use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use DB;

class WishController extends Controller
{
   public function wish(Request $request){
        try{
            $params = $request->all();
            
            if (empty($params['wish_to']) || empty($params['wish_by']) || empty($params['wish_message'])) {
                $data['status'] = "Error";
                $data['message'] = "Missing params";
                $status = 400;
                return response()->json($data, $status);
                exit;
            } 
            
            $wishes = new Wish();
            $wishes->wish_to = $params['wish_to'];
            $wishes->wish_by = $params['wish_by'];
            $wishes->wish_message = $params['wish_message'];
            $wishes->save();

            $data['status'] = "Success";
            $data['message'] = "Wish Sent Successfully";
            $status = 201;

            return response()->json($data, $status);

        } catch (Exception $e){

            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
   }

   public function getMyWish($id){
        $data = [];
        try{
            $wishes = DB::table('wishes')
            ->leftJoin('family_members', 'wishes.wish_by', '=', 'family_members.id')
            ->leftJoin('families', 'wishes.wish_by', '=', 'families.id')
            ->where('wishes.wish_to', $id)
            ->select(
                'wishes.wish_by', 
                'wishes.wish_message', 
                'family_members.first_name as member_first_name', 
                'family_members.middle_name as member_middle_name', 
                'family_members.last_name as member_last_name', 
                'families.head_first_name as family_first_name',
                'families.head_middle_name as family_middle_name',
                'families.head_last_name as family_last_name',
                )
            ->get();

            
            $data['status'] = "Success";
            $data['data'] = $wishes;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }

   }
}
