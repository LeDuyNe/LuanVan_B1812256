<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\QuestionBankRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\DetailQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionBank_Questions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuestionBankController extends AbstractApiController
{
    public function getQuestionBank()
    {
        $questionsBankId = QuestionBank::where('creatorId', auth()->id())->get('id');
        $data = array();
        $data_question = array();
        foreach ($questionsBankId as $questionBankId) {
            $questionBank['general']['main'] = QuestionBankResource::collection(QuestionBank::where('id', $questionBankId['id'])->get());
            $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId['id'])->pluck('questionId');
            $easyQuestion = 0;
            $normalQuestion = 0;
            $difficultQuestion = 0;
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

            $questionBank['general']['infoQuestion'] =  $totalQuestion;
            $questionBank['question'] = $data_question;
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

        $questionBankId = $validated_request['id'];

        $data = array();
        $data_question = array();

        $questionBank['general']['main'] = QuestionBankResource::collection(QuestionBank::where('id', $questionBankId)->get());
        $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId)->pluck('questionId');

        $easyQuestion = 0;
        $normalQuestion = 0;
        $difficultQuestion = 0;

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

        $questionBank['general']['infoQuestion'] =  $totalQuestion;
        $questionBank['question'] = $data_question;
        if ($questionBank) {
            $this->setData($questionBank);
            $this->setStatus('200');
            $this->setMessage("Get QuestionBank succesfully !");
        } else {
            $this->setStatus('200');
            $this->setMessage("Get QuestionBank failed !");
        }


        return $this->respond();
    }

    public function createQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $categoryId = $validated_request['categoryId'];
        $nameQuestionBank = Str::lower($validated_request['name']);
        $quizList = $validated_request['newQuizList'];
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
                    'creatorId' => $userId,
                ]);

                foreach ($quizList as $quiz) {
                    $content = $quiz['content'];
                    $level = $quiz['level'];

                    $question = Question::create([
                        'content' => $content,
                        'level' => $level,
                    ]);

                    $questionBank_questions = QuestionBank_Questions::create([
                        'questionBankId' => $questionBank['id'],
                        'questionId' => $question['id'],
                    ]);

                    $correctAnswer = DetailQuestion::create([
                        'content' => $quiz['correctAnswer'],
                        'isCorrect' => 1,
                        'questionId' => $question['id'],
                    ]);

                    foreach ($quiz['inCorrectAnswer'] as $quiz) {
                        DetailQuestion::create([
                            'content' => $quiz,
                            'isCorrect' => 0,
                            'questionId' => $question['id'],
                        ]);
                    }
                }
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

    // public function adddQuestionBank(QuestionBankRequests $request)
    // {
    //     $validated_request = $request->validated();

    //     $questionBankId = $validated_request['id'];
    //     $quizList = $validated_request['newQuizList'];
    //     $userId = auth()->id();

    //     $checkQuestionBank = QuestionBank::where(['id' => $questionBankId, 'creatorId' => $userId])->first();
    //     if ($checkQuestionBank) {
    //         foreach ($quizList as $quiz) {
    //             $content = $quiz['content'];
    //             $level = $quiz['level'];

    //             $question = Question::create([
    //                 'content' => $content,
    //                 'level' => $level,
    //             ]);

    //             $questionBank_questions = QuestionBank_Questions::create([
    //                 'questionBankId' => $questionBank['id'],
    //                 'questionId' => $question['id'],
    //             ]);

    //             $correctAnswer = DetailQuestion::create([
    //                 'content' => $quiz['correctAnswer'],
    //                 'isCorrect' => 1,
    //                 'questionId' => $question['id'],
    //             ]);

    //             foreach ($quiz['inCorrectAnswer'] as $quiz) {
    //                 DetailQuestion::create([
    //                     'content' => $quiz,
    //                     'isCorrect' => 0,
    //                     'questionId' => $question['id'],
    //                 ]);
    //             }
    //         }
    //         if ($question) {
    //             $this->setMessage("Add new questions is successfully !");
    //             return $this->respond();
    //         } else {
    //             $this->setMessage("Add new questions is fail !");
    //             return $this->respond();
    //         }
    //     }
    // }

    public function deleteQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $questionBank = QuestionBank::FindOrFail($questionBankId);

        $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId)->pluck('questionId');

        if ($questionsId) {
            $this->setStatus('400');
            $this->setMessage("Failed, you have to delete all questions belong question bank before deleting it");
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

        $question = Question::where('id',$questionId);
        $questionBank_question = QuestionBank_Questions::where('questionId',$questionId);
        $detailQuestionsId =  DetailQuestion::where(['questionId' =>  $questionId])->pluck('id')->toArray();

        if ($question && $questionBank_question &&  $detailQuestionsId) {
            $question->delete();
            $questionBank_question->delete();

            foreach ($detailQuestionsId as $detailQuestionId) {
                $detailQuestion = DetailQuestion::where('id',$detailQuestionId);
                $detailQuestion->delete();
            }

            $this->setStatus('200');
            $this->setMessage("Delete successfully");
        } else {
            $this->setStatus('400');
            $this->setMessage("Delete fail");
        }

        return $this->respond();
    }
}
