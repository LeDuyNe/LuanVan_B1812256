<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use App\Http\Resources\User as UserResource;
use App\Models\User;

class AdminController extends BaseController
{
    public function getUsers(Request $request)
    {
        $users = User::all()->except(Auth::id());
        return $this->sendResponse($users, 'List all users');
    }

    public function delegate(Request $request){
        $validator = Validator::make($request->all(), [
            'userID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $user = User::where('id',$request->userID)->update(['role'=> 1]);

        return $this->sendResponse($user, 'Delegating successfully.');
    }
}
