<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
       
        if(Auth::check()){
            return response()->json("success", 200);
        }else{
            return response()->json("unauthorized Token", 402);
        }
        
    }
    
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:7',
        ]);

        //try to login the user
        if (Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')], $request->has('remember'))) {
            // Authentication passed...
            return response()->json("Login Successfully", 200);
        } else {
            // Authentication failed...
            //redirect the user with the old input
            return redirect('/')->withInput()->with('info', 'Invalid Credentials!');
        }
    }

    public function logout()
    {
        //logout the user
        Auth::logout();
        return response()->json("Successfully logged out!", 200);
    }

}