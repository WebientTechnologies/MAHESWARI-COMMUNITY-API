<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Family;
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
        $phoneNumber = $request->input('phone_number');
        $verificationCode = '123456'; // The verification code sent to the user

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key='AIzaSyBC92PdmZt6iytdT5-dRIEsNJ6x5j-kO1I'",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "phoneNumber" => $phoneNumber,
                "recaptchaToken" => "",
                "iosReceipt" => "",
                "androidReceipt" => "",
                "requestType" => "phoneVerification",
                "code" => $verificationCode
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        print_r($response);exit;
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json([
                'message' => 'Failed to send OTP'
            ]);
        } else {
            return response()->json([
                'message' => 'OTP sent successfully'
            ]);
        }
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
        if (! $token = auth('family_head')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $a_token = $this->createNewToken($token);
        $data['status'] = "Success";
        $data['token'] = $a_token;
        return response()->json($data, 200);
    }
}
