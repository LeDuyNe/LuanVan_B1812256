<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\AuthorizationRequests;
use App\Http\Controllers\AbstractApiController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends AbstractApiController
{
    public function register(AuthorizationRequests $request)
    { 
        $validated_request = $request->validated();

        $user = User::create([
            'name' => $validated_request['name'],
            'email' => $validated_request['email'],
            'role' => 2,      //    Role (0) admin, (1) for teachers, (2) for students
            'password' => Hash::make($validated_request['password'])
        ]);

        $this->setData(new UserResource($user));
        $this->setStatus(JsonResponse::HTTP_CREATED);
        $this->setMessage("Register successfully!");
        
        return $this->respond();
    }

    public function login(AuthorizationRequests $request)
    {
        if (!$token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized', 'token' => $request->validated()], 401);
        }

        $user = new UserResource(auth()->user('id'));
        $success['bearer-token'] =  $user->createToken('authToken')->plainTextToken;
        $success['user'] =  $user;

        $this->setData($success);
        $this->setStatus('200');
        $this->setMessage("User login successfully !");
        
        return $this->respond();
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function permissionError(){
        $this->setMessage("You don't have permission !");
        
        return $this->respond();
    }
}
