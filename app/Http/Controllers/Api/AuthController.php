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
            'avartar' => $validated_request['avartar'] ?? null,
            'role' => $validated_request['role'],      //    Role (0) admin, (1) for teachers, (2) for students
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

    public function updatePassword(AuthorizationRequests $request)
    {
        $validated_request = $request->validated();

        if (!Hash::check($validated_request['oldPassword'], auth()->user()->password)) {
            $this->setStatus(400);
            $this->setMessage("Old Password doesn't match!");
        } else {
            User::whereId(auth()->user()->id)->update([
                'password' => Hash::make($validated_request['newPassword'])
            ]);
            $this->setStatus(200);
            $this->setMessage("Password changed successfully!");
        }
        return $this->respond();
    }

    public function updateInfo(AuthorizationRequests $request)
    {
        $validated_request = $request->validated();

        $oldName = User::where('id', auth()->user()->id)->pluck('name')->toArray();
        $oldRole = $validated_request['role'] ?? User::where('id', auth()->user()->id)->pluck('role')->toArray();
        $oldAvarta = User::where('id', auth()->user()->id)->pluck('avartar')->toArray();

        $name = $validated_request['name'] ?? $oldName[0];
        $role =  $validated_request['role'] ?? $oldRole[0];
        $avartar =  $validated_request['avartar'] ?? $oldAvarta[0];

        $user = User::whereId(auth()->user()->id)->update([
            'name' => $name,
            'avartar' => $avartar,
            'role' => $role,      //    Role (0) admin, (1) for teachers, (2) for students
        ]);

        if ($user) {
            $this->setStatus('200');
            $this->setMessage("Update information of user successfully.");
        } else {
            $this->setStatus('400');
            $this->setMessage("Update information of user fail.");
        }
        return $this->respond();
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function permissionError()
    {
        $this->setMessage("You don't have permission !");

        return $this->respond();
    }
}
