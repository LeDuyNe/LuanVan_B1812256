<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamRequests;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Examinfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamController extends AbstractApiController
{    
    public function getExams(){
        
    }

    public function getExam(ExamRequests $request){

    }

    public function createExam(ExamRequests $request)
    {       
        $validated_request = $request->validated(); 
        // $uniqueid = Str::random(9);
        
        // $examinfo = Examinfo::create([
        //         'userID' => Auth::id(),
        //         'course' => $request->course,
        //         'total_questions' => $request->total_questions,
        //         'uniqueid' => $uniqueid,
        //         'time' => $request->time,
        //         'status' => $request->status,
        //         'timeActive' => $request->timeActive
        //     ]);

        //     $success['data'] =  $examinfo;
        //     return $this->sendResponse($success, 'Create exam successfully.');
    }

    public function updateCategory(ExamRequests $request)
    {
        // $validated_request = $request->validated();
        // $name_category = Str::lower($validated_request['name']);
        // $userId = auth()->id();

        // $checkCategory = Category::where(['creatorId' => $userId, 'name' => $name_category])->first();

        // if (!$checkCategory) {
        //     $category = Category::where('id', $validated_request['id'])->update($request->all());

        //     $this->setStatus('200');
        //     $this->setMessage("Update category successfully.");

        //     return $this->respond();
        // }
        // $this->setStatus('400');
        // $this->setMessage("Category is existed");

        // return $this->respond();
    }


    public function deleteExam(ExamRequests $request)
    {
        // $validated_request = $request->validated();

        // $category = Category::FindOrFail($validated_request['id']);
        // if ($category->delete()) {
        //     $this->setStatus('200');
        //     $this->setMessage("Delete successfully");

        //     return $this->respond();
        // }
        // $this->setMessage("Delete Failed");

        // return $this->respond();
    }

}
