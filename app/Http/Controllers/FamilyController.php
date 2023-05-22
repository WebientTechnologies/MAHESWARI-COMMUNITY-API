<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Family;
use App\Models\FamilyMember;
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
                'families.head_occupation AS occupation',
                'families.marital_status',
                ]);
            
           
                $members = $members->merge($head);
                 
                $data['status'] = "Success";
                $data['data'] = $members;
                                        
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
                'families.head_occupation AS occupation',
                'families.marital_status',
                ]);
            
            
                $members = $members->merge($head);
                 
                $data['status'] = "Success";
                $data['data'] = $members;
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

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search in the 'families' table
        $families = Family::where('head_first_name', 'LIKE', "%$query%")
            ->orWhere('head_middle_name', 'LIKE', "%$query%")
            ->orWhere('head_last_name', 'LIKE', "%$query%")
            ->get();

        // Search in the 'family_members' table
        $familyMembers = FamilyMember::where('first_name', 'LIKE', "%$query%")
            ->orWhere('middle_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->get();

        return response()->json([
            'families' => $families,
            'familyMembers' => $familyMembers
        ]);
    }

    public function show()
    {
        $families = Family::with('members')->get();

    $result = [];

    foreach ($families as $family) {
        $head = [
            'head_first_name' => $family->head_first_name,
            'head_middle_name' => $family->head_middle_name,
            'head_last_name' => $family->head_last_name,
            'head_mobile_number' => $family->head_mobile_number,
        ];

        $members = $family->members->map(function ($member) {
            return [
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'last_name' => $member->last_name,
                'dob' => $member->dob,
                'relationship_with_head' => $member->relationship_with_head,
            ];
        });

        $result[] = [
            'head' => $head,
            'members' => $members,
        ];
        }

        return response()->json($result);
    }

}
 