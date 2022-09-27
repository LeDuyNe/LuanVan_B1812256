<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\AdminRequests;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends AbstractApiController
{
    public function getUsers()
    {
        $users = UserResource::collection(User::all()->except(Auth::id()));

        $this->setData($users);
        $this->setStatus('200');
        $this->setMessage("List all users");

        return $this->respond();
    }

    public function delegate(AdminRequests $request)
    {
        $validated_request = $request->validated();

        $user = User::where('id', $validated_request['id'])->update(['role' => 1]);
        $user = new UserResource(User::where('id',  $validated_request['id'])->first());

        $this->setData($user);
        $this->setStatus('200');
        $this->setMessage("Delegating successfully");

        return $this->respond();
    }

    public function delete(AdminRequests $request)
    {
        $validated_request = $request->validated();
        
        $user = User::FindOrFail($validated_request['id']);
        if ($user->delete()) {
            $this->setStatus('200');
            $this->setMessage("Delete successfully");

            return $this->respond();
        }
        $this->setMessage("Delete Failed");

        return $this->respond();
    }
}
