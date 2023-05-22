<?php

namespace App\Http\Controllers;
use App\Models\Business;
use App\Models\FamilyMember;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use DB;

class BusinessController extends Controller
{
    public function index()
    {
        $data = [];
        try {
            $businesses = DB::table('businesses')
                ->where('businesses.deleted_at', null)
                ->leftJoin('categories as cat', 'businesses.category_id', '=', 'cat.id')
                ->leftJoin('subcategories as subcat', 'businesses.subcategory_id', '=', 'subcat.id')
                ->orderBy('id', 'DESC')
                ->get([
                    'businesses.id',
                    'businesses.business_name',
                    'businesses.owner_name',
                    'cat.id AS category_id',
                    'cat.name AS category_name',
                    'subcat.id AS subcategory_id',
                    'subcat.name AS subcategory_name',
                    'businesses.subcategory_id',
                    'businesses.address',
                    'businesses.contact_number',
                    'businesses.created_at',
                    'businesses.updated_at',
                    'businesses.deleted_at',
                ]);

            $data['status'] = "Success";
            $data['data'] =  $businesses;

            // foreach ($businesses as $business) {
            //     $subcatIds = $business->subcategory_id;
            //     $subcatIds = json_decode($subcatIds, true);
            //     $subcategories = [];

            //     foreach ($subcatIds as $subcatId) {
            //         $subcategory = DB::table('subcategories')
            //             ->where('subcategories.id', $subcatId)
            //             ->get(['subcategories.id', 'subcategories.name']);

            //         $subcategories[] = $subcategory;
            //     }

            //     $business->sub_category = $subcategories;
            //     $data['data'][] = $business;
            // }

            return response()->json($data, 200);
        } catch (Exception $e) {
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }
public function getBusinessForFamily(Request $request){

    $data =[];
    $logedInUserId = $request->id;
    $role = $request->role;

    if($role == 'family_head'){
        $familyMemberIds = DB::table('family_members')
        ->where('family_id', $logedInUserId)
        ->pluck('id')
        ->all();

        $businesses = DB::table('businesses')
        ->whereIn('businesses.owner_id', $familyMemberIds)
        ->orWhere('businesses.owner_id', $logedInUserId)
        ->where('businesses.deleted_at', null)
        ->leftJoin('categories as cat', 'businesses.category_id', '=', 'cat.id')
        ->leftJoin('subcategories as subcat', 'businesses.subcategory_id', '=', 'subcat.id')
        ->orderBy('id', 'DESC')
        ->get([
            'businesses.id',
            'businesses.business_name',
            'businesses.owner_name',
            'businesses.owner_id',
            'cat.id AS category_id',
            'cat.name AS category_name',
            'subcat.id AS subcategory_id',
            'subcat.name AS subcategory_name',
            'businesses.subcategory_id',
            'businesses.address',
            'businesses.file',
            'businesses.contact_number',
            'businesses.created_at',
            'businesses.updated_at',
            'businesses.deleted_at',
        ]);

        $media = [];
        foreach ($businesses as $business) {
            $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($business->file, now()->addMinutes(10));
            $media[] = ["file" => $temporarySignedUrl];
        }

        $mergedBusiness = collect($businesses)->map(function ($item, $key) use ($media) {
            $item->media = $media[$key];
            return $item;
        });

    }
    if($role == 'family_member'){
        $head = FamilyMember::where('id',$logedInUserId)->first('family_id');
        $head_id = $head->family_id;
        $businesses = DB::table('businesses')
        ->Where('businesses.owner_id', $logedInUserId)
        ->orWhere('businesses.owner_id', $head_id)
        ->where('businesses.deleted_at', null)
        ->leftJoin('categories as cat', 'businesses.category_id', '=', 'cat.id')
        ->leftJoin('subcategories as subcat', 'businesses.subcategory_id', '=', 'subcat.id')
        ->orderBy('id', 'DESC')
        ->get([
            'businesses.id',
            'businesses.business_name',
            'businesses.owner_name',
            'businesses.owner_id',
            'cat.id AS category_id',
            'cat.name AS category_name',
            'subcat.id AS subcategory_id',
            'subcat.name AS subcategory_name',
            'businesses.subcategory_id',
            'businesses.address',
            'businesses.file',
            'businesses.contact_number',
            'businesses.created_at',
            'businesses.updated_at',
            'businesses.deleted_at',
        ]);

        $media = [];
        foreach ($businesses as $business) {
            $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($business->file, now()->addMinutes(10));
            $media[] = ["file" => $temporarySignedUrl];
        }

        $mergedBusiness = collect($businesses)->map(function ($item, $key) use ($media) {
            $item->media = $media[$key];
            return $item;
        });

    }
    $data['status'] = "Success";
    $data['data'] = $mergedBusiness;
    $status = 200;

    return response()->json($data, $status);
}

    public function show($id)
    {
        $data = [];
        try {
            $business = DB::table('businesses')
                ->where('businesses.id', '=', $id)   
                ->where('businesses.deleted_at', null)
                ->leftJoin('categories as cat', 'businesses.category_id', '=', 'cat.id')
                ->leftJoin('subcategories as subcat', 'businesses.subcategory_id', '=', 'subcat.id')
                ->get([
                    'businesses.id',
                    'businesses.business_name',
                    'businesses.owner_name',
                    'cat.id AS category_id',
                    'cat.name AS category_name',
                    'subcat.id AS subcategory_id',
                    'subcat.name AS subcategory_name',
                    'businesses.subcategory_id',
                    'businesses.address',
                    'businesses.contact_number',
                    'businesses.created_at',
                    'businesses.updated_at',
                    'businesses.deleted_at',
                ]);

            $data['status'] = "Success";
            $data['data'] = $business;

            // $subcatIds = $business[0]->subcategory_id;
            // $subcatIds = json_decode($subcatIds, true);

            // $subcategory = DB::table('subcategories')
            //     ->whereIn('subcategories.id', $subcatIds)
            //     ->get(['subcategories.id', 'subcategories.name']);

            // $business->sub_category = $subcategory;
            // $data['data'][] = $business;

            return response()->json($data, 200);
        } catch (Exception $e) {
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function store(Request $request){
       
        try{
            $params = $request->all();
            
            if (empty($params['business_name']) || empty($params['owner_name']) || empty($params['category_id'] )) {
                $data['status'] = "Error";
                $data['message'] = "Missing params";
                $status = 400;
                return response()->json($data, $status);
                exit;
            } 

            if (Business::where('business_name', $params['business_name'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "This Business name is already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            }

            if($request->hasFile('file')) {
                $allowedfileExtension=['jpg','png','jpeg', 'avif'];
                $file = $request->file('file'); 
                $errors = [];
    
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension,$allowedfileExtension);
                if($check) {
                    $name = 'community-'.time().'.'.$extension;
                    Storage::disk('s3')->put($name, file_get_contents($file));     
                }
                
            }
            
            $business = new Business();
            $business->business_name = $params['business_name'];
            $business->owner_name = $params['owner_name'];
            $business->owner_id = $params['owner_id'];
            $business->file = $name;
            $business->category_id = $params['category_id'];
            $business->subcategory_id = $request->subcategory_id; 
            $business->address = $request->address;
            $business->contact_number = $request->contact_number;
            $business->save();
            

            $data['status'] = "Success";
            $data['message'] = "Business Created";
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
            $params = $request->all();
            if (empty($params['business_name']) || empty($params['owner_name']) || empty($params['category_id'] )){
                $data['status'] = "Error";
                $data['message'] = "Missing params.";
                $status = 400;
                return response()->json($data, $status);
                exit;
            }
            
            if (Business::where('business_name', $params['business_name'])->where('id','!=',$params['id'])->exists()) {
                $data['status'] = "Error";
                $data['message'] = "Business name is already exists.";
                $status = 409;
                return response()->json($data, $status);
                exit;
            }
            $business = Business::find($params['id']);
            if(!empty($business)){
                if($request->hasFile('file')) {
                    $allowedfileExtension=['jpg','png','jpeg', 'avif'];
                    $file = $request->file('file'); 
                    $errors = [];
        
                    $extension = $file->getClientOriginalExtension();
                    $check = in_array($extension,$allowedfileExtension);
                    if($check) {
                        $name = 'community-'.time().'.'.$extension;
                        Storage::disk('s3')->put($name, file_get_contents($file));     
                    }
                    $business->file = $name;
                }
            
                $business->business_name = $params['business_name'];
                $business->owner_name = $params['owner_name'];
                
                $business->category_id = $params['category_id'];
                $business->subcategory_id = $request->subcategory_id; 
                $business->address = $request->address;
                $business->contact_number = $request->contact_number;
                $business->save();
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
            
            $business = Business::find($id);
            if(empty($business)){

                $data['status'] = "Error";
                $data['message'] = "business not found.";
                $status = 401;
                return response()->json($data, $status);
                exit;
            }
            $business->delete();

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
