<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ResultRequest;
use App\Http\Resources\ResultResource;
use App\Http\Resources\UserResultResource;
use App\Models\Answer;
use App\Models\DetailQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;

class ResultController extends AbstractApiController
{
    public function getResult(ResultRequest $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $creatorId = auth()->id();

        $checkQuestionBank =  QuestionBank::where("creatorId", $creatorId)->where("id", $questionBankId)->get();
        if ($checkQuestionBank) {
            $resultsId =  Result::where("questionBankId", $questionBankId)->pluck('id')->toArray();
            $data = array();
            foreach ($resultsId as $resultId) {
                $result =  Result::where("id", $resultId)->get();
                $examineeId = Result::where("id", $resultId)->pluck('examineeId')->toArray();
                $userResult = ResultResource::collection($result);
                $userInfo = UserResultResource::collection(User::where("id", $examineeId[0])->get());

                $object_result = ([
                    'user' => $userInfo,
                    'result' => $userResult
                ]);

                array_push($data, $object_result);
            }
            if ($data) {
                $this->setData(($data));
                $this->setStatus('200');
                $this->setMessage("Get result succefully !");
            } else {
                $this->setStatus('400');
                $this->setMessage("Get result fail !");
            }
        } else {
            $this->setStatus('400');
            $this->setMessage("Result is not exsited !");
        }
        return $this->respond();
    }

    public function getDetailResult(ResultRequest $request)
    {
        $validated_request = $request->validated();

        $resultId = $validated_request['id'];
        $examineeId = Result::where("id", $resultId)->pluck('examineeId')->toArray();

        $result['user'] = UserResultResource::collection(User::where("id", $examineeId[0])->get());
        $result['main'] = ResultResource::collection(Result::where("id", $resultId)->get());

        $answersId = Answer::where("resultId", $resultId)->pluck('answerId')->toArray();
        $answer = [];
        foreach ($answersId as $answerId) {
            $questionId = DetailQuestion::where("id", $answerId)->pluck('questionId')->toArray();
            $question = Question::where("id", $questionId[0])->pluck('content')->toArray();
            $isCorrect = DetailQuestion::where("id", $answerId)->pluck('isCorrect')->toArray();
            if (!$isCorrect[0]) {
                $correctAnswer = DetailQuestion::where("questionId", $questionId[0])->where('isCorrect', 1)->pluck('content');
            } else {
                $correctAnswer = null;
            }
            $chooseAnswer = DetailQuestion::where("id", $answerId)->pluck('content');
            $data = ([
                "question" => $question,
                "correctAnswer" => $correctAnswer,
                "chooseAnswer" => $chooseAnswer
            ]);
            array_push($answer, $data);
        };
        $result['sub'] = $answer;
        $this->setData($result);
        $this->setStatus('200');
        $this->setMessage("Get result succefully !");
        return $this->respond();
    }

    public function updateResult(ResultRequest $request)
    {
        $validated_request = $request->validated();
        $resultId = $validated_request['id'];

        $oldRestTime = Result::where("id", $resultId)->pluck('restTime')->toArray();
        $numOldCorrect = Result::where("id", $resultId)->pluck('numCorrect')->toArray();
        $oldNote = Result::where("id", $resultId)->pluck('note')->toArray();
        $tempNumOldCorrect = explode("/", $numOldCorrect[0]);

        $restTime = $validated_request['restTime'] ?? $oldRestTime;
        $note = $validated_request['note'] ?? $oldNote;
        $numTrueAnswer = $validated_request['numTrueAnswer'] ?? $tempNumOldCorrect[0];

        $result = Result::where("id", $resultId)
            ->update([
                'restTime' => $restTime,
                'note' => $note,
                'numCorrect' => $numTrueAnswer . '/' . $tempNumOldCorrect[1]
            ]);
        
            if($result){
                $result =  ResultResource::collection(Result::where('id', $resultId)->get());
                $this->setData($result);
                $this->setStatus('200');
                $this->setMessage("Update result succefully !");
            }else{
                $this->setStatus('400');
                $this->setMessage("Update result failed !");
            }

            return $this->respond();
    }
}
