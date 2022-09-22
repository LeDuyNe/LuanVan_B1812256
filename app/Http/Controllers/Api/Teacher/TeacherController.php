<?php

namespace App\Http\Controllers\API\Teacher;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends BaseController
{
    public function index(Request $request)
    {
        return $this->sendResponse(null, 'This is dashboard of teaccher');
    }
}
