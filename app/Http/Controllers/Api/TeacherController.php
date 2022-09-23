<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;

class TeacherController extends BaseController
{
    public function index(Request $request)
    {
        return $this->sendResponse(null, 'This is dashboard of teaccher');
    }
}
