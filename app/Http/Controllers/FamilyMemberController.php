<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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

class FamilyMemberController extends Controller
{
    public function __construct() {
        $this->middleware('auth:member', ['except' => ['login', 'register', 'verifyEmail']]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
           
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:family_members',
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            'dob' => 'date_format:Y-m-d',
            'mobile_number' => 'string|max:10',
            'address' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        // $ip_address = $request->ip();
        // $otp = rand(100000, 999999);
        $familymember = new FamilyMember();
        $familymember->name = $request->input("name");
        $familymember->email = $request->input("email");
        $familymember->password = Hash::make($request->password);
        $familymember->occupation = $request->input("occupation");
        $familymember->mobile_number = $request->input("mobile_number");
        $familymember->age = $request->input("age");
        $familymember->family_id = $request->input("family_id");
        $familymember->dob = $request->input("dob");
        $familymember->address = $request->input("address");
        $familymember->save();
            
        return new JsonResponse(
            [
                'success' => true, 
                'message' => 'Successful created Family Member.', 
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
            'member' => auth('member')->user()
        ]);
    }

    public function login(Request $request){
        $data = [];
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('member')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $a_token = $this->createNewToken($token);
        $data['status'] = "Success";
        $data['token'] = $a_token;
        return response()->json($data, 200);
    }
}
