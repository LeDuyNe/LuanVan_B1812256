<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamRequests;
use App\Models\Category;
use App\Models\Exams;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ExamController extends AbstractApiController
{
    public function getExams()
    {
    }

    public function getExam(ExamRequests $request)
    {
    }

    public function createExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $name_exam = Str::lower($validated_request['name']);
        $categoryId = $validated_request['timeStart'];
        $name = Str::lower($validated_request['name']);
        $newQuizList = $validated_request['newQuizList'];
        $timeDuration = $validated_request['timeDuration'];
        $timeStart =  gmdate("Y-m-d H:i:s", $validated_request['timeStart']);
        $countLimit = $validated_request['countLimit'];

        $userId = auth()->id();

        $checkExam = Exams::where(['creatorId' => $userId, 'name' => $name_exam])->first();
        if (!$checkExam) {
            $category = Category::create([
                'name' => $name_category,
                'creatorId' => $userId
            ]);

        $exam = Exams::create([
            'name' => $name,
            'timeDuration' => $timeDuration,
            'timeStart' => $timeStart,
            'countLimit' => $countLimit,
            'creatorId' => Auth::id(),
        ]);

        $this->setStatus('200');
        $this->setMessage("Successfully !");

        return $this->respond();
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
