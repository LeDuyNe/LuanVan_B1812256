<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamineesRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\ResultResource;
use App\Models\ChooseAnswer;
use App\Models\DetailQuestion;
use App\Models\Exams;
use App\Models\Question;
use App\Models\Result;
use Carbon\Carbon;

class ExamineesController extends AbstractApiController
{
    public function getExam(ExamineesRequests $request)
    {
        $validated_request = $request->validated();
        $numExamination = $validated_request['id'];

        $arrayExamsId = Exams::where('numExamination', $numExamination)->pluck('id')->toArray();
        $randomElement = array_rand($arrayExamsId, 1);
        $examId = $arrayExamsId[$randomElement];

        $isPublish =  Exams::where('id', $examId)->pluck('isPublished')->toArray();
        $timeStart = Exams::where('id', $examId)->pluck('timeStart')->toArray();
        $countLimit = Exams::where('id', $examId)->pluck('countLimit')->toArray();
        $examineeId = auth()->id();

        $countLimitExam = 0;
        foreach ($arrayExamsId as $id) {
            $exitResult = Result::where("examineeId", $examineeId)->where("examId", $id)->pluck('id')->toArray();
            $countLimitExam += count($exitResult);
        }

        if ($isPublish[0] == 1 && $timeStart[0] <= Carbon::now()) {
            if ($countLimitExam < $countLimit[0]) {
                $data_question = array();
                $data['general']['main'] = ExamResource::collection(Exams::where('id', $examId)->get());

                $questionsId = Exams::where('id', $examId)->pluck('arrayQuestion')->toArray();

                $easyQuestion = 0;
                $normalQuestion = 0;
                $difficultQuestion = 0;
                $questionsId = json_decode($questionsId[0], true);

                foreach ($questionsId as $questionId) {
                    $question['content'] = QuestionResource::collection(Question::where('id', $questionId)->get());
                    $levelQuestion = Question::where('id', $questionId)->pluck('level');
                    if ($levelQuestion[0] == 1) {
                        $easyQuestion++;
                    } elseif ($levelQuestion[0]  == 2) {
                        $normalQuestion++;
                    } else {
                        $difficultQuestion++;
                    }

                    $data_detailQuestion = array();
                    $detailQuestionsId = DetailQuestion::where('questionId', $questionId)->pluck('id');
                    foreach ($detailQuestionsId as $detailQuestionId) {
                        $detailQuestion =  DetailQuestionResource::collection(DetailQuestion::where('id', $detailQuestionId)->get());
                        array_push($data_detailQuestion, $detailQuestion);
                    }

                    $question['answer'] = $data_detailQuestion;
                    array_push($data_question, $question);
                }

                $totalQuestion = [
                    'esay'  => $easyQuestion,
                    'normal' => $normalQuestion,
                    'difficult' => $difficultQuestion,
                    'total' =>  $easyQuestion + $normalQuestion + $difficultQuestion
                ];

                $data['general']['sub'] =  $totalQuestion;
                $data['question'] = $data_question;

                $this->setData($data);
                $this->setStatus('200');
                $this->setMessage("Get exam succefully !");
            } else {
                $this->setStatus('400');
                $this->setMessage("The number of attempts has exceeded the limit");
            }
        } else {
            $this->setStatus('400');
            $this->setMessage("It's not time for an exam !");
        }

        return $this->respond();
    }

    public function getResult(ExamineesRequests $request)
    {
        $examineeId = auth()->id();
        $result =  Result::where("examineeId", $examineeId)->get();
        $result->groupBy('examId');
        $this->setData(ResultResource::collection($result));
        $this->setStatus('200');
        $this->setMessage("Get result succefully !");
        return $this->respond();
    }

    public function getDetailResult(ExamineesRequests $request)
    {
        $validated_request = $request->validated();

        $resultId = $validated_request['id'];
        $result['main'] = ResultResource::collection(Result::where("id", $resultId)->get());

        $answersId = ChooseAnswer::where("resultId", $resultId)->pluck('answerId')->toArray();
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

    public function submitExam(ExamineesRequests $request)
    {
        $validated_request = $request->validated();

        $restTime = $validated_request['restTime'];
        $examId = $validated_request['examId'];
        $examineeId = auth()->id();
        $answersId = $validated_request['answerIds'];

        $result = Result::create([
            'restTime' => $restTime,
            'examineeId' => $examineeId,
            'examId' =>   $examId,
        ]);

        $numTrueAnswer = 0;
        $totalQuestion = 0;
        $resultId =  $result['id'];

        foreach ($answersId as $answerId) {
            $answer = ChooseAnswer::create([
                'answerId' => $answerId,
                'resultId' => $resultId,
            ]);

            $isCorrect = DetailQuestion::where('id', $answerId)->pluck('isCorrect')->toArray();
            if ($isCorrect[0] == 1) {
                $numTrueAnswer++;
            }

            $totalQuestion++;
        }

        $result = Result::where("id", $result['id'])
            ->update([
                'numCorrect' => $numTrueAnswer . '/' . $totalQuestion
            ]);

        $result =  ResultResource::collection(Result::where('id', $resultId)->get());
        if ($result) {
            $this->setData($result);
            $this->setStatus('200');
            $this->setMessage("Submit exam succefully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Submit exam failed !");
        }

        return $this->respond();
    }
}
