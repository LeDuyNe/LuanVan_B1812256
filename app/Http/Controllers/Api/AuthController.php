<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;
use App\Models\User;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'userID' => 'required|string|min:5|unique:users',
            'password' => 'required|string|min:6',
            're_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'userID' => $request->userID,
            'role' => 2,      //    Role (0) admin, (1) for teachers, (2) for students
            'password' => Hash::make($request->password)
        ]);

        $success['token'] =  $user->createToken('auth_token')->plainTextToken;
        $success['fullname'] =  $user->fullname;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // dd($request->email);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = new UserResource(Auth::user());
            $success['user'] =  $user;
            $success['bearer-token'] =  $user->createToken('authToken')->plainTextToken;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return ['message' => 'Logged out'];
    }
}
