<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Examinfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Ui\Presets\React;

class ExamController extends BaseController
{
    public function getCategories(Request $request){    
        $categories = Category::where('creatorId', Auth::id())->get('name');
        return $this->sendResponse($categories, 'List categories');
    }

    public function createCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
;
        $checkCategory = Category::where(['creatorId' => Auth::id(), 'name' => Str::lower($request->name)])->first();
        if(!$checkCategory){
            $category = Category::create([
                'name' => Str::lower($request->name),
                'creatorId' => Auth::id()
            ]);
            $success['category'] =  $request->name;
            return $this->sendResponse($success, 'Create category successfully.');
        }
        return $this->sendError('Category is exist',[], 400);
    }
    
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
