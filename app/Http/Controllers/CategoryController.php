<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use DB;

class CategoryController extends Controller
{
    // public function __construct(){

    //     $this->middleware('auth');
    // }

    public function index(){
        
        $data = [];
        try{
            $categories = Category::with('subcategories')
                ->leftJoin('users as ucb', 'categories.created_by', '=', 'ucb.id')
                ->leftJoin('users as uub', 'categories.updated_by', '=', 'uub.id')
                ->leftJoin('users as udb', 'categories.deleted_by', '=', 'udb.id')
                ->orderBy('categories.id', 'DESC')
                ->get([
                    'categories.id',
                    'categories.name',
                    'ucb.id AS created_by_uid',
                    'ucb.name AS created_by_name',
                    'uub.id AS updated_by_uid',
                    'uub.name AS updated_by_name',
                    'udb.id AS deleted_by_uid',
                    'udb.name AS deleted_by_name',
                    'categories.created_at',
                    'categories.updated_at',
                    'categories.deleted_at',
                ]);


           
            $data['status'] = "Success";
            $data['data'] = $categories;

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
            $categories = DB::table('categories')
            ->where('id', '=', $id)
            ->get(['id','name']);
             
            $data['status'] = "Success";
            $data['data'] = $categories;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function store(Request $request){
       
        try{
            $params = $request->json()->all();
            
            if (empty($params['name'])) {
                $data['status'] = "Error";
                $data['message'] = "Missing params";
                $status = 400;
                return response()->json($data, $status);
                exit;
            } 

            if (Category::where('name', $params['name'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "This Category name is already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            }
            

            $categories = new Category();
            $categories->name = $params['name'];
            $categories->created_by = Auth::user()->id;
            $categories->save();

            $data['status'] = "Success";
            $data['message'] = "Category Created";
            $status = 201;

            return response()->json($data, $status);

        } catch (Exception $e){

            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function edit(Request $request){

        $data = [];
        try{
            $params = $request->json()->all();

            if (empty($params['id']) || empty($params['name'])){
                $data['status'] = "Error";
                $data['message'] = "Missing params.";

                $status = 400;
                return response()->json($data, $status);
                exit;

            }

            
            if (Category::where('name', $params['name'])->where('id','!=',$params['id'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "Category name is already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            }

            $categories = Category::find($params['id']);

            if(!empty($categories)){
            
                $categories->name = $params['name'];
                $categories->updated_by = Auth::user()->id;
                $categories->save();

                $data['status'] = "Success";
                $data['message'] = "Record Updated.";
                $status = 200;
                
                    
            }else{
                $data['status'] = "Error";
                $data['message'] = "No data found";
                $status = 400;
            }
            
            return response()->json($data, $status);
          
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function destroy(Request $request, $id){

        $data = [];
        try{
            
            $categories = Category::find($id);
            
            if(empty($categories)){

                $data['status'] = "Error";
                $data['message'] = "Cateogry not found.";
                $status = 401;
                return response()->json($data, $status);
                exit;
            }

            $categories->deleted_by = Auth::user()->id;
            $categories->save();
            $categories->delete();

            $data['status'] = "Success";
            $data['message'] = "Record deleted.";
            $status = 200;
            
            return response()->json($data, $status);
            

        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }
}
