<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends BaseController
{
    public function getUsers(Request $request)
    {
        $users = UserResource::collection(User::all()->except(Auth::id()));
        return $this->sendResponse($users, 'List all users');
    }

    public function delegate(Request $request){
        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $user = User::where('id',$request->userId)->update(['role'=> 1]);
        $user = new UserResource(User::where('id',$request->userId)->first());
    
        return $this->sendResponse($user, 'Delegating successfully.');
    }
}
