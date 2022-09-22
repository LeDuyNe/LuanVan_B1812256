<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Http\Resources\User as UserResource;
use App\Models\User;

class AdminController extends BaseController
{
    public function getUsers(Request $request)
    {
        $users = User::all()->except(Auth::id());
        return $this->sendResponse($users, 'List all users');
    }
}
