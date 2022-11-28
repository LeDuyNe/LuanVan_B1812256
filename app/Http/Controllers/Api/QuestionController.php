<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\QuestionRequests;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

class QuestionController extends AbstractApiController
{
    public static function getQuestionsByExamId(QuestionRequests $request){
        $validated_request = $request->validated();
        $examId = $validated_request['examId'];

        $questions = QuestionResource::collection(Question::where('examId', $examId)->get());

        $this->setData($questions);
        $this->setStatus('200');
        $this->setMessage("List all exams");

        return $this->respond();
    }
}
