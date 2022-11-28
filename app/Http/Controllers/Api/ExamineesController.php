<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamineesRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\ResultResource;
use App\Models\Answer;
use App\Models\DetailQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionBank_Questions;
use App\Models\Result;
use Carbon\Carbon;

class ExamineesController extends AbstractApiController
{
    public function getExam(ExamineesRequests $request)
    {
        $validated_request = $request->validated();
        $numExamination = $validated_request['id'];

        $questionBankId = QuestionBank::where('numExamination', $numExamination)->pluck('id')->toArray();

        $isPublish =  QuestionBank::where('id', $questionBankId)->pluck('isPublished')->toArray();
        $timeStart = QuestionBank::where('id', $questionBankId)->pluck('timeStart')->toArray();
        $countLimit = QuestionBank::where('id', $questionBankId)->pluck('countLimit')->toArray();
        $structureExam = QuestionBank::where('id', $questionBankId)->pluck('structureExam')->toArray();
        $structureExam = json_decode($structureExam[0], true);
        $examineeId = auth()->id();

        $countLimitExam = 0;
        $exitResult = Result::where("examineeId", $examineeId)->where("questionBankId", $questionBankId)->pluck('id')->toArray();
        $countLimitExam += count($exitResult);

        if ($isPublish[0] == 1 && $timeStart[0] <= Carbon::now()) {
            if ($countLimitExam < $countLimit[0]) {
                $data_question = array();
                $data['general'] = QuestionBankResource::collection(QuestionBank::where('id', $questionBankId)->get());

                $numEasy = $structureExam['easy'];
                $numNormal = $structureExam['normal'];
                $numDifficult = $structureExam['difficult'];

                $easy = 1;
                $normal = 2;
                $difficult = 3;

                $arrayQuestionsEasy = $this->getQuestionsId($questionBankId, $easy);
                $arrayQuestionsNormal = $this->getQuestionsId($questionBankId, $normal);
                $arrayQuestionsDifficult = $this->getQuestionsId($questionBankId, $difficult);

                $randomQuestionEasy = $this->randomQuestion($arrayQuestionsEasy, $numEasy);
                $randomQuestionNormal = $this->randomQuestion($arrayQuestionsNormal, $numNormal);
                $randomQuestionDifficult = $this->randomQuestion($arrayQuestionsDifficult, $numDifficult);

                $arrayQuestionsId = [];
                if (is_array($randomQuestionEasy) == true) {
                    foreach ($randomQuestionEasy as $question) {
                        array_push($arrayQuestionsId, $arrayQuestionsEasy[$question]);
                    }
                } else {
                    array_push($arrayQuestionsId, $arrayQuestionsEasy[$randomQuestionEasy]);
                }

                if (is_array($randomQuestionNormal) == true) {
                    foreach ($randomQuestionNormal as $question) {
                        array_push($arrayQuestionsId, $arrayQuestionsNormal[$question]);
                    }
                } else {
                    array_push($arrayQuestionsId, $arrayQuestionsNormal[$randomQuestionNormal]);
                }

                if (is_array($randomQuestionDifficult) == true) {
                    foreach ($randomQuestionDifficult as $question) {
                        array_push($arrayQuestionsId, $arrayQuestionsDifficult[$question]);
                    }
                } else {
                    array_push($arrayQuestionsId, $arrayQuestionsDifficult[$randomQuestionDifficult]);
                }

                $question = [];
                foreach ($arrayQuestionsId as $questionId) {
                    $data_detailQuestion = array();
                    $detailQuestionsId = DetailQuestion::where('questionId', $questionId)->pluck('id');
                    foreach ($detailQuestionsId as $detailQuestionId) {
                        $detailQuestion =  DetailQuestionResource::collection(DetailQuestion::where('id', $detailQuestionId)->get());
                        array_push($data_detailQuestion, $detailQuestion);
                    }

                    $question = ([
                        'content' => QuestionResource::collection(Question::where('id', $questionId)->get()),
                        'answer' => $data_detailQuestion
                    ]);

                    array_push($data_question, $question);
                }

                $data['sub'] = $data_question;

                $this->setData($data);
                $this->setStatus('200');
                $this->setMessage("Get exam successfully !");
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
        $this->setMessage("Get result successfully !");
        return $this->respond();
    }

    public function getDetailResult(ExamineesRequests $request)
    {
        $validated_request = $request->validated();

        $resultId = $validated_request['id'];
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
        $this->setMessage("Get result successfully !");
        return $this->respond();
    }

    public function submitExam(ExamineesRequests $request)
    {
        $validated_request = $request->validated();

        $restTime = $validated_request['restTime'] ?? 0;
        $questionBankId = $validated_request['questionBankId'];
        $examineeId = auth()->id();
        $answersId = $validated_request['answerIds'];
        $note = $validated_request['note'] ?? null;

        $result = Result::create([
            'restTime' => $restTime,
            'note' => $note,
            'examineeId' => $examineeId,
            'questionBankId' =>   $questionBankId,
        ]);

        $numTrueAnswer = 0;
        $structureExam = QuestionBank::where('id', $questionBankId)->pluck('structureExam')->toArray();
        $structureExam = json_decode($structureExam[0], true);
        $totalQuestion = $structureExam['easy'] + $structureExam['normal'] + $structureExam['difficult'];
        $resultId =  $result['id'];

        foreach ($answersId as $answerId) {
            $answer = Answer::create([
                'answerId' => $answerId,
                'resultId' => $resultId,
            ]);

            $isCorrect = DetailQuestion::where('id', $answerId)->pluck('isCorrect')->toArray();
            if ($isCorrect[0] == 1) {
                $numTrueAnswer++;
            }
        }

        $result = Result::where("id", $result['id'])
            ->update([
                'numCorrect' => $numTrueAnswer . '/' . $totalQuestion
            ]);

        $result =  ResultResource::collection(Result::where('id', $resultId)->get());
        if ($result) {
            $this->setData($result);
            $this->setStatus('200');
            $this->setMessage("Submit exam successfully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Submit exam failed !");
        }

        return $this->respond();
    }

    public function getQuestionsId($questionBankId, $level)
    {
        $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId)->pluck('questionId')->toArray();
        $data = [];
        foreach ($questionsId as $questionId) {
            $levelQuestion = Question::where('id', $questionId)->pluck('level')->toArray();
            if ($level == $levelQuestion[0]) {
                array_push($data, $questionId);
            }
        }
        return $data;
    }

    public function randomQuestion($arrayId, $num)
    {
        $randomElement = array_rand($arrayId, $num);
        return $randomElement;
    }
}
