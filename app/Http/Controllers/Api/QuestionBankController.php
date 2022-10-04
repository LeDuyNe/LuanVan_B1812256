<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\QuestionBankRequests;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuestionBankController extends AbstractApiController
{
    public function getQuestionBank()
    {
        $questionsBankId = QuestionBank::where('creatorId', auth()->id())->get('id');

        $data = array();
        foreach ($questionsBankId as $questionBankId) {
            $questionBank['general'] = QuestionBankResource::collection(QuestionBank::where('id', $questionBankId['id'])->get());
            $questionBank['questions'] = QuestionResource::collection(Question::where('questionBankId', $questionBankId['id'])->get());
            array_push($data, $questionBank);
        }

        $this->setData($data);
        $this->setStatus('200');
        $this->setMessage("List all exams");

        return $this->respond();
    }

    public function getDetailQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();
        $questionsBankId = $validated_request['id'];

        $questionBank['general'] = QuestionBankResource::collection(QuestionBank::where('id', $questionsBankId)->get());
        $questionBank['questions'] = QuestionResource::collection(Question::where('questionBankId', $questionsBankId)->get());

        $this->setData($questionBank);
        $this->setStatus('200');
        $this->setMessage("Succefully !");
        return $this->respond();
    }

    public function createQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $categoryId = $validated_request['categoryId'];
        $nameQuestionBank = Str::lower($validated_request['name']);
        $newQuizList = $validated_request['newQuizList'];
        $note = NULL;

        if (!empty($validated_request['note'])) {
            $note = $validated_request['note'];
        }

        $userId = auth()->id();

        $checkActiveCategory = Category::where(['id' => $categoryId, 'isPublished' => 1])->first();
        if (!$checkActiveCategory) {
            $this->setMessage("The category must be activated!");
            return $this->respond();
        } else {
            $checkQuestionBank = QuestionBank::where(['creatorId' => $userId, 'name' => $nameQuestionBank])->first();
            if (!$checkQuestionBank) {
                $questionBank = QuestionBank::create([
                    'name' => $nameQuestionBank,
                    'note' => $note,
                    'categoryId' => $categoryId,
                    'creatorId' => Auth::id(),

                ]);

                $questionEasy = 0;
                $questionNormal = 0;
                $questionDifficult = 0;

                foreach ($newQuizList as $question) {
                    $content = $question['content'];
                    $correctAnswer = $question['correctAnswer'];
                    $inCorrectAnswer = $question['inCorrectAnswer'];
                    $level = $question['level'];

                    if ($level == 1) {
                        $questionEasy++;
                    } elseif ($level == 2) {
                        $questionNormal++;
                    } else {
                        $questionDifficult++;
                    }

                    $question = Question::create([
                        'content' => $content,
                        'correctAnswer' => $correctAnswer,
                        'inCorrectAnswer' => json_encode($inCorrectAnswer),
                        'level' => $level,
                        'questionBankId' => $questionBank['id'],
                    ]);
                }

                $info = array();
                array_push($info, $questionEasy, $questionNormal, $questionDifficult);

                QuestionBank::where('id', $questionBank['id'])->where('creatorId', auth()->id())->update([
                    "info" => json_encode($info)
                ]);

                if ($question) {
                    $this->setData(new QuestionBankResource($questionBank));
                    $this->setMessage("Creat Exam is successfully !");
                    return $this->respond();
                } else {
                    $this->setMessage("Creat Exam is fail !");
                    return $this->respond();
                }
            } else {
                $this->setMessage("Name of exam is existed!");
                return $this->respond();
            }
        }
    }

    public function adddQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $newQuizList = $validated_request['newQuizList'];
        $userId = auth()->id();

        $checkQuestionBank = QuestionBank::where(['id' => $questionBankId, 'creatorId' => $userId])->first();
        if ($checkQuestionBank) {
  
            $questionEasy = 0;
            $questionNormal = 0;
            $questionDifficult = 0;

            foreach ($newQuizList as $question) {
                $content = $question['content'];
                $correctAnswer = $question['correctAnswer'];
                $inCorrectAnswer = $question['inCorrectAnswer'];
                $level = $question['level'];

                if ($level == 1) {
                    $questionEasy++;
                } elseif ($level == 2) {
                    $questionNormal++;
                } else {
                    $questionDifficult++;
                }

                // $question = Question::create([
                //     'content' => $content,
                //     'correctAnswer' => $correctAnswer,
                //     'inCorrectAnswer' => json_encode($inCorrectAnswer),
                //     'level' => $level,
                //     'questionBankId' => $questionBankId,
                // ]);
            }

            $infoQuestionBank = QuestionBank::where(['id' => $questionBankId, 'creatorId' => $userId])->pluck('info');

            // $infoQuestionEasy = $infoQuestionBank[0];
            // $infoQuestionNormal = $infoQuestionBank[1];
            // $infoQuestionDifficult = $infoQuestionBank[2];
            foreach($infoQuestionBank[0] as $level){
                dd($level);
            }
            // dd($infoQuestionBank[0]);
            die();

            $info = array();
            array_push(
                $info,
                $questionEasy + $infoQuestionEasy,
                $questionNormal + $infoQuestionNormal,
                $questionDifficult + $infoQuestionDifficult
            );

            QuestionBank::where('id', $questionBankId)->where('creatorId', $userId)->update([
                "info" => json_encode($info)
            ]);

            if ($question) {
                $this->setData(new QuestionBankResource($checkQuestionBank));
                $this->setMessage("Add new questions is successfully !");
                return $this->respond();
            } else {
                $this->setMessage("Add new questions is fail !");
                return $this->respond();
            }
        }
    }

    public function deleteQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $questionBank = QuestionBank::FindOrFail($questionBankId);
        // dd($questionBank);
        // die();
        $questionsId = QuestionBank::where(['id' =>  $questionBankId])->pluck('id')->toArray();
        if ($questionsId) {
            $this->setStatus('400');
            $this->setMessage("Failed, you have to delete exams before deleting question bank");
            return $this->respond();
        } else {
            if ($questionBank->delete()) {
                $this->setStatus('200');
                $this->setMessage("Delete successfully");

                return $this->respond();
            }
            $this->setMessage("Delete Failed");

            return $this->respond();
        }
    }
}
