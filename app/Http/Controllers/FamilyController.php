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

        $familyMember = FamilyMember::where('mobile_number', $request->mobile)->first();

        if (!$familyMember) {
            $head = Family::where('head_mobile_number', $request->monile)->first();

            if (!$head) {
                $data['status'] = "Error";
                $data['message'] = 'Please Enter Valid Mobile Number';
                $status = 400;
                return response()->json($data, $status);
            } else {
                $data['status'] = "Success";
                $data['role'] = "family_head";
                $data['data'] = $head;
            }
        } else {
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


}
