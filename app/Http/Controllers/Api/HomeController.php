<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    public function index(Request $request){
            return $this->sendError('Error');
    }

    public function permissionError(Request $request){
        return $this->sendError("You don't have permission");
}
}
