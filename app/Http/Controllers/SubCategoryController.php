<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\User;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use DB;


class SubCategoryController extends Controller
{

   

    public function index(){
        
        $data = [];
        try{
            
            $subCategories = DB::table('subcategories')
            ->where('subcategories.deleted_at', null)
            ->leftJoin('categories', 'subcategories.category_id', '=', 'categories.id')
            ->orderBy('id','DESC')
            ->get(['subcategories.id','subcategories.name','subcategories.category_id','categories.name AS category_name']);
            

            $data['status'] = "Success";
            $data['data'] = $subCategories;

            return response()->json($data, 200);

        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function getSubCategories(){
        
        $data = [];
        try{
           
            $subCategories = DB::table('subcategories')
            ->where('subcategories.deleted_at', null)
            ->leftJoin('categories', 'subcategories.category_id', '=', 'categories.id')
            ->orderBy('id','DESC')
            ->get(['subcategories.id','subcategories.name','subcategories.category_id','categories.name AS category_name']);
            

            $data['status'] = "Success";
            $data['data'] = $subCategories;

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
            $subCategories = DB::table('subcategories')
            ->where('subcategories.id', '=', $id)
            ->leftJoin('categories', 'subcategories.category_id', '=', 'categories.id')
            ->get(['subcategories.id','subcategories.name','subcategories.category_id','categories.name AS category_name']);
    
            $data['status'] = "Success";
            $data['data'] = $subCategories;

            return response()->json($data, 200);
          
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function getByCat($catid){
        $data = [];
        try{
            $subCategories = DB::table('subcategories')
            ->where('subcategories.category_id', '=', $catid)
            ->where('subcategories.deleted_at', '=', null)
            ->get(['id','name']);  
            $data['status'] = "Success";
            $data['data'] = $subCategories;

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
            if (empty($params['name']) || empty($params['category_id']) ) {
                $data['status'] = "Error";
                $data['message'] = "Missing params";
                $status = 400;
                return response()->json($data, $status);
                exit;
            } 

            if (SubCategory::where('name', $params['name'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "Sub Category name already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            }
            

            $subCategories = new SubCategory();
            $subCategories->name = $params['name'];
            $subCategories->category_id = $params['category_id'];
            $subCategories->created_by = Auth::user()->id;
            $subCategories->save();

            $data['status'] = "Success";
            $data['message'] = "Sub-category Created";
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

            if (SubCategory::where('name', $params['name'])->where('id','!=',$params['id'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "SubCategory name already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            } 
            
            $subCategories = SubCategory::find($params['id']);
            
            if(!empty($subCategories)){
                
                $subCategories->name = $params['name'];
                $subCategories->category_id = $params['category_id'];
                $subCategories->updated_by = Auth::user()->id;
                
                    $subCategories->save();

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

    public function destroy($id){

        $data = [];
        try{
            $subCategories = SubCategory::find($id);

            if(empty($subCategories)){
                $data['status'] = "Error";
                $data['message'] = "Sub-category not found.";
                $status = 401;

                return response()->json($data, $status);
                exit;
            }

            $subCategories->deleted_by = Auth::user()->id;
            $subCategories->save();
            $subCategories->delete();

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
