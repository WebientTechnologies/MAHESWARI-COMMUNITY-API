<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerifyEmail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use App\Models\Request as ChangeRequest;
use Illuminate\Support\Facades\Storage;

class FamilyController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth:family_head', ['except' => ['login', 'register', 'verifyEmail']]);
    // }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
           
            'head_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:families',
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            'dob' => 'date_format:Y-m-d',
            'head_mobile_number' => 'string|max:10',
            'address' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        // $ip_address = $request->ip();
        // $otp = rand(100000, 999999);
        $family = new Family();
        $family->head_name = $request->input("head_name");
        $family->email = $request->input("email");
        $family->password = Hash::make($request->password);
        $family->head_occupation = $request->input("head_occupation");
        $family->head_mobile_number = $request->input("head_mobile_number");
        $family->head_age = $request->input("head_age");
        $family->dob = $request->input("dob");
        $family->address = $request->input("address");
        $family->save();
            
        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Successful created Family head.', 
                //'token' => $token
            ], 
            201
        );
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 10080,
            'family_head' => auth('family_head')->user()
        ]);
    }

    function sendOtp(Request $request)
    {
       $data = [];
        $mobileNumber = $request->input('mobile_number');
        $familyMembers = FamilyMember::where('mobile_number', $mobileNumber)->get(['family_id']);

        if ($familyMembers->isEmpty()) {
            $headnumber = Family::where('head_mobile_number', $mobileNumber)->get(['head_mobile_number']);
           
            if($headnumber->isEmpty()){
                $data['status'] = "Error";
                $data['message'] = 'This Number is not registerd with us';
                $status = 400;
                return response()->json($data, $status);
                exit;
            
            }else{
                $number = $headnumber[0]['head_mobile_number'];
                $data['status'] = "Success";
                $data['number'] = $number;
                
            }
        }else{
            $fId = $familyMembers[0]['family_id'];
            $headnumber = Family::where('id', $fId)->get(['head_mobile_number']);
            $number = $headnumber[0]['head_mobile_number'];
            $data['status'] = "Success";
            $data['number'] = $number;

        }

        return response()->json($data, 200);

    }

    public function login(Request $request)
    {
        $data = [];

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->device_token == "" || $request->device_token == null){
            $data['status'] = "Error";
            $data['message'] = 'Invalid Login Token';
            $status = 400;
            return response()->json($data, $status);exit;
        }

        $familyMember = FamilyMember::where('mobile_number', $request->mobile)->first();

        if (!$familyMember) {
            $head = Family::where('head_mobile_number', $request->mobile)->first();

            if (!$head) {
                $data['status'] = "Error";
                $data['message'] = 'Please Enter Valid Mobile Number';
                $status = 400;
                return response()->json($data, $status);
            } else {
                $head->device_token = $request->device_token;
                $head->save();
                $data['status'] = "Success";
                $data['role'] = "family_head";
                $data['data'] = $head;
            }
        } else {
            $familyMember->device_token = $request->device_token;
            $familyMember->save();
            $data['status'] = "Success";
            $data['role'] = "family_member";
            $data['data'] = $familyMember;
        }

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $role = $request->role;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time().'.'.$image->getClientOriginalExtension();
                Storage::disk('s3')->put($filename, file_get_contents($image));
                
            }

            if($role == 'family_head'){
                $family = Family::findOrFail($id);
                $family->head_first_name = $request->input('first_name');
                $family->head_middle_name = $request->input('middle_name');
                $family->head_last_name = $request->input('last_name');
                $family->head_occupation = $request->input('occupation');
                $family->head_mobile_number = $request->input('mobile_number');
                $family->head_dob = $request->input('dob');
                $family->address = $request->input('address');
                $family->marital_status = $request->input('marital_status');
                $family->relationship_with_head = $request->input('relationship_with_head');
                $family->qualification = $request->input('qualification');
                $family->degree = $request->input('degree');
                $family->gender = $request->input('gender');
                $family->date_of_anniversary = $request->input('date_of_anniversary');
                $family->image = $filename;
                $family->save();

            }

            if($role == 'family_member'){
                $family = FamilyMember::findOrFail($id);
                $family->first_name = $request->input('first_name');
                $family->middle_name = $request->input('middle_name');
                $family->last_name = $request->input('last_name');
                $family->occupation = $request->input('occupation');
                $family->mobile_number = $request->input('mobile_number');
                $family->dob = $request->input('dob');
                $family->address = $request->input('address');
                $family->marital_status = $request->input('marital_status');
                $family->relationship_with_head = $request->input('relationship_with_head');
                $family->qualification = $request->input('qualification');
                $family->degree = $request->input('degree');
                $family->gender = $request->input('gender');
                $family->date_of_anniversary = $request->input('date_of_anniversary');
                $family->image = $filename;
                $family->save();

            }
            
            
            return response()->json([
                'success' => true,
                'message' => 'Family updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update family: ' . $e->getMessage()
            ]);
        }
    }

    public function getMyFamily($id, $role){
        $data = [];
        try{
            if($role == 'family_head'){
                $members = DB::table('family_members')
                ->where('family_members.family_id', '=', $id)
                ->where('family_members.deleted_at', '=', null)
                ->get([
                'family_members.id',
                'family_members.first_name',
                'family_members.middle_name',
                'family_members.last_name',
                'family_members.dob',
                'family_members.mobile_number',
                'family_members.address',
                'family_members.relationship_with_head',
                'family_members.qualification',
                'family_members.degree',
                'family_members.occupation',
                'family_members.marital_status',
                'family_members.gender',
                'family_members.date_of_anniversary',
                'family_members.image',
                ]);
 
                $head = DB::table('families')
                ->where('families.id',$id)
                ->get([
                'families.id',
                'families.head_first_name As first_name',
                'families.head_middle_name AS middle_name',
                'families.head_last_name AS last_name',
                'families.head_dob AS dob',
                'families.head_mobile_number AS mobile_number',
                'families.address',
                'families.relationship_with_head',
                'families.qualification',
                'families.degree',
                'families.gender',
                'families.date_of_anniversary',
                'families.image',
                'families.head_occupation AS occupation',
                'families.marital_status',
                ]);
            
           
                $members = $members->merge($head);

                $media = [];
                foreach ($members as $member) {
                    if ($member->image) {
                        $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($member->image, now()->addMinutes(10));
                        $media[] = ["image" => $temporarySignedUrl];
                    } else {
                        $media[] = ["image" => null]; // Set null for members without an image
                    }
                }

                $mergedMembers = $members->map(function ($item, $key) use ($media) {
                    $item->media = $media[$key];
                    return $item;
                });
           
                 
                $data['status'] = "Success";
                $data['data'] = $mergedMembers;
                                        
            }
            if($role == 'family_member'){
                $head = FamilyMember::where('id',$id)->first('family_id');
                $head_id = $head->family_id;
                $members = DB::table('family_members')
                ->where('family_members.family_id', '=', $head_id)
                ->where('family_members.deleted_at', '=', null)
                ->get([
                'family_members.id',
                'family_members.first_name',
                'family_members.middle_name',
                'family_members.last_name',
                'family_members.dob',
                'family_members.mobile_number',
                'family_members.address',
                'family_members.relationship_with_head',
                'family_members.qualification',
                'family_members.degree',
                'family_members.occupation',
                'family_members.marital_status',
                'family_members.gender',
                'family_members.date_of_anniversary',
                'family_members.image',
                ]);
            
            $head = DB::table('families')
            ->where('families.id',$head_id)
            ->get([
                'families.id',
                'families.head_first_name As first_name',
                'families.head_middle_name AS middle_name',
                'families.head_last_name AS last_name',
                'families.head_dob AS dob',
                'families.head_mobile_number AS mobile_number',
                'families.address',
                'families.relationship_with_head',
                'families.qualification',
                'families.degree',
                'families.gender',
                'families.date_of_anniversary',
                'families.image',
                'families.head_occupation AS occupation',
                'families.marital_status',
                ]);
            
            
                $members = $members->merge($head);
                $media = [];
                foreach ($members as $member) {
                    if ($member->image) {
                        $temporarySignedUrl = Storage::disk('s3')->temporaryUrl($member->image, now()->addMinutes(10));
                        $media[] = ["image" => $temporarySignedUrl];
                    } else {
                        $media[] = ["image" => null]; // Set null for members without an image
                    }
                }

                $mergedMembers = $members->map(function ($item, $key) use ($media) {
                    $item->media = $media[$key];
                    return $item;
                });
           
                 
                $data['status'] = "Success";
                $data['data'] = $mergedMembers;
            }
            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function editMemberByHead(Request $request, $id){
        $data = [];
        try{
            // print_r($request->role);exit;
            if($request->role == 'family_head'){

                $family = FamilyMember::findOrFail($id);

                if (!$family) {
                    $data['status'] = "Error";
                    $data['message'] = "Member not found";
                    $status = 404;
                    return response()->json($data, $status);
                    exit;
                }

                if (FamilyMember::where('mobile_number', $request->mobile_number)->where('id', '!=', $id)->exists()) {
                    $data['status'] = "Error";
                    $data['message'] = "This Mobile Number is already exists.";
                    $status = 409;
                    return response()->json($data, $status);
                    exit;
                }

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename = time().'.'.$image->getClientOriginalExtension();
                    Storage::disk('s3')->put($filename, file_get_contents($image));
                    
                }
                    $family->first_name = $request->input('first_name');
                    $family->middle_name = $request->input('middle_name');
                    $family->last_name = $request->input('last_name');
                    $family->occupation = $request->input('occupation');
                    $family->mobile_number = $request->input('mobile_number');
                    $family->dob = $request->input('dob');
                    $family->address = $request->input('address');
                    $family->marital_status = $request->input('marital_status');
                    $family->relationship_with_head = $request->input('relationship_with_head');
                    $family->qualification = $request->input('qualification');
                    $family->degree = $request->input('degree');
                    $family->gender = $request->input('gender');
                    $family->date_of_anniversary = $request->input('date_of_anniversary');
                    $family->image = $filename;
                    $family->save();
                
                $data['status'] = "Success";
                $data['message'] = 'Member details Updated';

                return response()->json($data, 200);

            }else{

                $data['status'] = "Error";
                $data['message'] = 'You are not authorized to Edit the Member';

                return response()->json($data, 409);

            }
            
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

    public function addMemberByHead(Request $request, $familyId){
        $data = [];
        try{
            // print_r($request->role);exit;
            if($request->role == 'family_head'){

                if (FamilyMember::where('mobile_number', $request->mobile_number)->exists()) {
                    $data['status'] = "Error";
                    $data['message'] = "This Mobile Number is already exists.";
                    $status = 409;
                    return response()->json($data, $status);
                    exit;
                }

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename = time().'.'.$image->getClientOriginalExtension();
                    Storage::disk('s3')->put($filename, file_get_contents($image));
                    
                }

                $family = new FamilyMember;
                $family->family_id = $familyId;
                $family->first_name = $request->input('first_name');
                $family->middle_name = $request->input('middle_name');
                $family->last_name = $request->input('last_name');
                $family->occupation = $request->input('occupation');
                $family->mobile_number = $request->input('mobile_number');
                $family->dob = $request->input('dob');
                $family->address = $request->input('address');
                $family->marital_status = $request->input('marital_status');
                $family->relationship_with_head = $request->input('relationship_with_head');
                $family->qualification = $request->input('qualification');
                $family->degree = $request->input('degree');
                $family->gender = $request->input('gender');
                $family->date_of_anniversary = $request->input('date_of_anniversary');
                $family->image = $filename;
                $family->save();
                
                $data['status'] = "Success";
                $data['message'] = 'Created Member';

                return response()->json($data, 200);

            }else{

                $data['status'] = "Error";
                $data['message'] = 'You are not authorized to Add the Member';

                return response()->json($data, 409);

            }
            
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }

     public function getMyRequest($id){
        $data = [];
        try{
            $requests = DB::table('requests')
            ->where('requests.head_id', '=', $id)
            ->where('requests.status','=', 'pending')
            ->leftjoin('family_members as fm', 'requests.member_id', '=', 'fm.id')
            ->get(['requests.id',
            'requests.column_name',
            'requests.old_value',
            'requests.new_value',
            'fm.id As member_id',
            'fm.first_name As member_first_name',
            'fm.middle_name As member_middle_name',
            'fm.last_name As member_last_name',
            'requests.status',
            'requests.created_at',
            'requests.updated_at',
            'requests.deleted_at',
            ]);

            foreach ($requests as $request) {
                if ($request->column_name === 'Business Image') {
                    $request->new_value = Storage::disk('s3')->temporaryUrl($request->new_value, now()->addMinutes(10));
                }
            }  
             
            $data['status'] = "Success";
            $data['data'] = $requests;

            return response()->json($data, 200);
        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
     }

     public function approve(Request $request, $requestId){
        $req = ChangeRequest::findOrFail($requestId);
        $column = $req->column_name;
        $newValue = $req->new_value;
        $memberId = $req->member_id;
        $status = $req->status;
        if($status == 'approved'){
            return response()->json(['message' => 'request already Approved']);
            exit;
        }
        if($column == 'Business Image'){
            $business = Business::where('owner_id', $memberId)->where('file', $newValue)->first();
            //print_r($business->is_image_approved);exit;
            if($request->status == 'approved'){
                $business->is_image_approved = 1;
            }
            if($request->status == 'rejected'){
                $business->is_image_approved = 0;
            }
            $business->save();
        }
        $member = FamilyMember::findOrFail($memberId);
        if($column == "first_name"){
            $member->first_name = $newValue;
        }
        if($column == "middle_name"){
            $member->middle_name = $newValue;
        }
        if($column == "last_name"){
            $member->last_name = $newValue;
        }
        if($column == "occupation"){
            $member->occupation = $newValue;
        }
        if($column == "dob"){
            $member->dob = $newValue;
        }
        if($column == "mobile_number"){
            $member->mobile_number = $newValue;
        }
        if($column == "address"){
            $member->address = $newValue;
        }
        if($column == "relationship_with_head"){
            $member->relationship_with_head = $newValue;
        }
        if($column == "marital_status"){
            $member->marital_status = $newValue;
        }
        if($column == "qualification"){
            $member->qualification = $newValue;
        }
        if($column == "degree"){
            $member->degree = $newValue;
        }
        $member->save();
        $req->status = $request->status;
        $req->save();
        return response()->json(['message' => 'Status Changed']);
     }


     public function profile($id, $role)
        {
            $data= [];
            if($role == 'family_member'){
                $profile = FamilyMember::where('id', $id)->first();
                $data['status'] = "Success";
                $data['role'] = "family_member";
                $data['data'] = $profile;
            }

            if ($role == 'family_head') {
                $profile = Family::where('id', $id)->first();
                $data['status'] = "Success";
                $data['role'] = "family_head";
                $data['data'] = $profile;
            }

            return response()->json($data, 200);
        }

        public function searchLastName(Request $request)
        {
            $query = $request->input('query');
    
            $families = Family::where('head_last_name', 'LIKE', '%'.$query.'%')
            ->distinct()
                ->get(['head_last_name']);
    
            return response()->json($families, 200);
        }

        public function familyDirectory(Request $request)
        {
            $firstName = $request->input('first_name');
            $middleName = $request->input('middle_name');
            $lastName = $request->input('last_name');
            //$gender = $request->input('gender');
            $marital = $request->input('marital');
            $relationship = $request->input('relation');
            $qualification = $request->input('qualification');
            $degree = $request->input('degree');
            $occupation = $request->input('occupation');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $membersQuery = DB::table('family_members')
                ->whereNull('family_members.deleted_at')
                ->leftJoin('families as fa', 'family_members.family_id', '=', 'fa.id')
                ->where('family_members.first_name', 'LIKE', '%'.$firstName.'%')
                //->orWhere('family_members.first_name', 'LIKE', 'null')
                ->where('family_members.middle_name', 'LIKE', '%'.$middleName.'%')
                //->orWhere('family_members.middle_name', 'LIKE', 'null')
                ->where('family_members.last_name', 'LIKE', '%'.$lastName.'%')
                //->orWhere('family_members.last_name', 'LIKE', 'null')
                //->where('family_members.gender', 'LIKE', '%'.$gender.'%')
                ->where('family_members.marital_status', 'LIKE', '%'.$marital.'%')
                ->orWhere('family_members.marital_status', 'LIKE', 'null')
            ->where('family_members.relationship_with_head', 'LIKE', '%'.$relationship.'%')
                //->orWhere('family_members.relationship_with_head', 'LIKE', 'null')
                ->where('family_members.qualification', 'LIKE', '%'.$qualification.'%')
                //->orWhere('family_members.qualification', 'LIKE', 'null')
                ->where('family_members.degree', 'LIKE', '%'.$degree.'%')
                //->orWhere('family_members.degree', 'LIKE', 'null')
                ->where('family_members.occupation', 'LIKE', '%'.$occupation.'%')
                //->orWhere('family_members.occupation', 'LIKE', 'null')
            ->whereBetween('family_members.dob',[$start_date,$end_date])
                ->select(
                    'family_members.id',
                    'family_members.first_name',
                    'family_members.middle_name',
                    'family_members.last_name',
                    'family_members.dob',
                    'family_members.mobile_number',
                    'family_members.relationship_with_head',
                    'fa.head_first_name',
                    'fa.head_middle_name',
                    'fa.head_last_name',
                    'fa.head_mobile_number'
                );
                
        
            $members = $membersQuery->get();
    
            $totalGroup = count($members);
            $perPage = 2000;
            $page = Paginator::resolveCurrentPage('page');
        
            $members = new LengthAwarePaginator($members->forPage($page, $perPage), $totalGroup, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
    
            $u1 = json_encode($members,true);
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
           
            
            $data['current_page'] = $current_page;
            $data['data'] = $u4;
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
        }

}
 