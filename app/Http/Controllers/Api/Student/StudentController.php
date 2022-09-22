<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends BaseController
{
    public function index(Request $request)
    {
        return $this->sendResponse(null, 'This is dashboard of student');
    }
}
