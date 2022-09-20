<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


class AuthController extends Controller
{
    // Register
    public function register(Request $request) {
        $validator = Validator::make($request->all(),[ 
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'account' => 'required|string|min:5|max:50|unique:users',
            'userID' => 'required|string|min:5|unique:users',
            'role' => 'required|integer',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        if($validator->fails())return response()->json($validator->errors());
        $user = User::create([
           'fullname' => $request->fullname,
           'email' => $request->email,
           'account' => $request->account,
           'userID' => $request->userID,
           'role' => $request->role,
           'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(
        ['data' => $user,'access_token' => $token, 'token_type' => 'Bearer', ]);
    }

    ///login
    public function login(Request $request) {
        if (!Auth::attempt($request->only('account', 'password')))  {
           return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('account', $request['account'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => "'Hi '.$user->fullname",
           "access_token" => $token, "token_type" => "Bearer"]);
    }
    
    //logout
    public function logout()  {
        Auth::user()->tokens()->delete();
          return ['message' => 'Logged out'];
   }

}   
