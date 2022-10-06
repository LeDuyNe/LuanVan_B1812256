<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\QuestionBankRequests;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\DetailQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionBank_Question;
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

                // $levelEasy = 0;
                // $levelNormal = 0;
                // $levelDifficult = 0;

                foreach ($newQuizList as $content) {
                    $content = $content['content'];
                    $level = $content['level'];

                    $question = Question::create([
                        'content' => $content,
                        'level' => $level,
                    ]);

                    $questionBank_question = QuestionBank_Question::create([
                        'questionBankId' => $questionBank['id'],
                        'quesitonId' => $question['id'],
                    ]);

                    $contentCorrectAnswer = DetailQuestion::create([
                        'content' => $content['correctAnswer'],
                        'isCorrect' => 1,
                        'questionId' => $question['id'],
                    ]);

                    foreach($content['inCorrectAnswer'] as $value){
                        DetailQuestion::create([
                       'content' => $value[0],
                        'isCorrect' => 0,
                        'questionId' => $question['id'],

                    } 
                    // 'questionBankId' => $questionBank['id'],
                    // 'correctAnswer' => $correctAnswer,
                    // 'inCorrectAnswer' => json_encode($inCorrectAnswer),
                    // $correctAnswer = $question['correctAnswer'];
                    // $inCorrectAnswer = $question['inCorrectAnswer'];
 

                    // if ($level == 1) {
                    //     $levelEasy++;
                    // } elseif ($level == 2) {
                    //     $levelNormal++;
                    // } else {
                    //     $levelDifficult++;
                    // }


                }

                QuestionBank::where('id', $questionBank['id'])->where('creatorId', auth()->id())->update([
                    "info" => ['easy'=> $levelEasy, 'normal' => $levelNormal, 'difficult' => $levelDifficult]
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
  
            $levelNewEasy = 0;
            $levelNewNormal = 0;
            $levelNewDifficult = 0;

            foreach ($newQuizList as $question) {
                $content = $question['content'];
                $correctAnswer = $question['correctAnswer'];
                $inCorrectAnswer = $question['inCorrectAnswer'];
                $level = $question['level'];

                if ($level == 1) {
                    $levelNewEasy++;
                } elseif ($level == 2) {
                    $levelNewNormal++;
                } else {
                    $levelNewDifficult++;
                }

                $question = Question::create([
                    'content' => $content,
                    'correctAnswer' => $correctAnswer,
                    'inCorrectAnswer' => json_encode($inCorrectAnswer),
                    'level' => $level,
                    'questionBankId' => $questionBankId,
                ]);
            }

            $infoQuestionBank = QuestionBank::where(['id' => $questionBankId, 'creatorId' => $userId])->pluck('info');
            $infoQuestionBank = json_decode($infoQuestionBank[0], true);
            $levels = array_values($infoQuestionBank);

            $levelEasy = $levels[0];
            $levelNormal = $levels[1];
            $levelDifficult = $levels[2];


            QuestionBank::where('id', $questionBankId)->where('creatorId', $userId)->update([
                "info" => ['easy'=> $levelEasy + $levelNewEasy, 'normal' => $levelNormal + $levelNewNormal, 'difficult' => $levelDifficult + $levelNewDifficult]
            ]);

            if ($question) {
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

        $questionsId = Question::where(['id' =>  $questionBankId])->pluck('id')->toArray();
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

    public function deleteQuestion(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionId = $validated_request['id'];
        $question = Question::FindOrFail($questionId);
        
        $questionBankId = Question::where(['id' =>  $questionId])->pluck('questionBankId')->toArray();
        $infoQuestionBank = QuestionBank::where(['id' =>  $questionBankId])->where('creatorId', auth()->id())->pluck('info');

        if($question && $infoQuestionBank){
            $levelQuestion = Question::where(['id' =>  $questionId])->pluck('level')->toArray();

            $infoQuestionBank = json_decode($infoQuestionBank[0], true);
            $levels = array_values($infoQuestionBank);

            $levelEasy = $levels[0];
            $levelNormal = $levels[1];
            $levelDifficult = $levels[2];

  
            if($levelQuestion[0] == 1){
                $levelEasy =  $levelEasy - 1;
            }elseif($levelQuestion[0] == 2){
                $levelNormal =  $levelNormal - 1;
            }else{
                $levelDifficult = $levelDifficult - 1;
            }

            $question->delete();
            QuestionBank::where('id', $questionBankId)->where('creatorId', auth()->id())->update([
                "info" => ['easy'=> $levelEasy , 'normal' => $levelNormal, 'difficult' => $levelDifficult]
            ]);


            $this->setStatus('200');
            $this->setMessage("Delete successfully");

            return $this->respond();
        }else{
            $this->setStatus('400');
                $this->setMessage("Delete fail");
        }
    }
}
