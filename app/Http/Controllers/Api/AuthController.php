<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\AuthorizationRequests;
use App\Http\Controllers\AbstractApiController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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
            'nameTitle' => $validated_request['nameTitle'] ?? null,
            'password' => Hash::make($validated_request['password'])
        ]);

        $this->setData(new UserResource($user));
        $this->setStatus('200');
        $this->setMessage("Register successfully!");

        return $this->respond();
    }

    public function login(AuthorizationRequests $request)
    {
        if (!$token = auth()->attempt($request->validated())) {
            $this->setStatus('401');
            $this->setMessage("Unauthorized !");
        } else {
            $user = new UserResource(auth()->user('id'));
            $success['bearer-token'] =  $user->createToken('authToken')->plainTextToken;
            $success['user'] =  $user;

            $this->setData($success);
            $this->setStatus('200');
            $this->setMessage("User login successfully !");
        }
        
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
        $nameOldTitle = User::where('id', auth()->user()->id)->pluck('nameTitle')->toArray();
  
        $name = $validated_request['name'] ?? $oldName[0];
        $role =  $validated_request['role'] ?? $oldRole[0];
        $avartar =  $validated_request['avartar'] ?? $oldAvarta[0];
        // dd($avartar);
        $nameTitle = $validated_request['nameTitle'] ?? $nameOldTitle[0];

        $user = User::where('id',auth()->user()->id)->update([
            'name' => $name,
            'avartar' => $avartar,
            'role' => $oldRole[0],      //    Role (0) admin, (1) for teachers, (2) for students
            'nameTitle' => $nameTitle
        ]);

        if ($user) {
            $user = User::where('id', auth()->user()->id)->get();
            // $this->setData(new UserResource(User::whereId(auth()->user()->id));
            $this->setData(UserResource::collection($user));
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

        $this->setStatus('200');
        $this->setMessage("Successfully logged out !");
        return $this->respond();
    }

    public function permissionError()
    {
        $this->setStatus('400');
        $this->setMessage("You don't have permission !");
        return $this->respond();
    }
}
