<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Middleware\Examinees;
use App\Http\Requests\ExamineesRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Models\Answer;
use App\Models\DetailQuestion;
use App\Models\Examinee;
use App\Models\Exams;
use App\Models\Question;
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

        if ($isPublish[0] == 1 && $timeStart[0] <= Carbon::now()) {
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
            $this->setMessage("It's not time for an exam !");
        }

        return $this->respond();
    }

    public function submitExam(ExamineesRequests $request)
    {
        $validated_request = $request->validated();
        $restTime = $validated_request['restTime'];
        $emxamId = $validated_request['emxamId'];
        $examineeId = auth()->id();
        $answerIds = $validated_request['answerIds'];

        $result = Examinee::create([
            'restTime' => $restTime,
            'examineeId' => $examineeId,
            'emxamId' =>   $emxamId,
        ]);

        $numTrueAnswer = 0;
        foreach ($answerIds as $answerId) {
            $answer = Answer::create([
                'answerId' => $answerId,
                'resultId' => $result->id(),
            ]);

            $isCorrect = DetailQuestion::where('id', $answerId)->pluck('isCorrect')->toArray();
            if($isCorrect == 1){
                $numTrueAnswer ++;
            }
        }

        // $result = Examinee::update([
        //     "isPublished" => 1
        // ]);
    }

    public function randomQuestion($arrayId, $num)
    {
        $randomElement = array_rand($arrayId, $num);
        return $randomElement;
    }
}
