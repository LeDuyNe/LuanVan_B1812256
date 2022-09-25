<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Examinfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamInfoController extends BaseController
{
    public function getExams(Request $request){
        
    }
    public function createExam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course' => 'required|string|min:4|max:255',
            'total_questions' => 'required|integer',
            'time' => 'required|string',
            'status' => 'required|string',
            'timeActive' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $uniqueid = Str::random(9);

        $examinfo = Examinfo::create([
                'userID' => Auth::id(),
                'course' => $request->course,
                'total_questions' => $request->total_questions,
                'uniqueid' => $uniqueid,
                'time' => $request->time,
                'status' => $request->status,
                'timeActive' => $request->timeActive
            ]);

            $success['data'] =  $examinfo;
            return $this->sendResponse($success, 'Create exam successfully.');
    }
}
