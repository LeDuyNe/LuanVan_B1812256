<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\QuestionBankRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\OptionalResource;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\DetailQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionBank_Questions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuestionBankController extends AbstractApiController
{
    public function getQuestionBank()
    {
        $questionsBankId = QuestionBank::where('creatorId', auth()->id())->orderBy('created_at', 'DESC')->get('id');
        $data = array();

        foreach ($questionsBankId as $questionBankId) {
            $main =QuestionBank::where('id', $questionBankId['id'])->get();
            $categoryId = QuestionBank::where('id', $questionBankId['id'])->pluck('categoryId')->toArray();

            $questionBank['main'] =  QuestionBankResource::collection($main);
            $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId['id'])->pluck('questionId');

            $easyQuestion = 0;
            $normalQuestion = 0;
            $difficultQuestion = 0;

            foreach ($questionsId as $questionId) {
                $levelQuestion = Question::where('id', $questionId)->pluck('level');
                if ($levelQuestion[0] == 1) {
                    $easyQuestion++;
                } elseif ($levelQuestion[0]  == 2) {
                    $normalQuestion++;
                } else {
                    $difficultQuestion++;
                }
            }

            $totalQuestion = [
                'esay'  => $easyQuestion,
                'normal' => $normalQuestion,
                'difficult' => $difficultQuestion,
                'total' =>  $easyQuestion + $normalQuestion + $difficultQuestion
            ];
 
            $questionBank['sub'] =  $totalQuestion;
            $nameCategory = Category::where('id', $categoryId[0])->pluck('name')->toArray();
            // dd($nameCategory);
            $questionBank['optional'] = ([
                "categoryName" => $nameCategory[0]
            ]);
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

        $questionBank['general']['sub'] =  $totalQuestion;
        $questionBank['question'] = $data_question;

        if ($questionBank) {
            $this->setData($questionBank);
            $this->setStatus('200');
            $this->setMessage("Get QuestionBank succesfully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Get QuestionBank failed !");
        }

        return $this->respond();
    }

    public function createQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $categoryId = $validated_request['categoryId'];
        $nameQuestionBank = Str::lower($validated_request['name']);
        $quizList = $validated_request['questionList'];
        $note = $validated_request['note'] ?? null;
        $timeDuration = $validated_request['timeDuration'];
        $timeStart = Carbon::createFromTimestamp($validated_request['timeStart'])->toDateTimeString();
        $countLimit = $validated_request['countLimit'];
        $isPublished = $validated_request['isPublished'] ?? 0;
        $structureExam = $validated_request['structureExam'];
        $userId = auth()->id();

        $checkActiveCategory = Category::where(['id' => $categoryId, 'isPublished' => 1])->first();
        if (!$checkActiveCategory) {
            $this->setStatus('400');
            $this->setMessage("The category must be activated!");
        } else {
            $checkQuestionBank = QuestionBank::where(['creatorId' => $userId, 'name' => $nameQuestionBank])->first();

            $numExamination = rand(0, 99999);
            $statusNumExamination = QuestionBank::where('numExamination', $numExamination)->get();

            while (!$statusNumExamination) {
                $numExamination = rand(0, 99999);
                $statusNumExamination = QuestionBank::where('numExamination', $numExamination)->get();
            }
            if (!$checkQuestionBank) {
                $questionBank = QuestionBank::create([
                    'name' => $nameQuestionBank,
                    'note' => $note,
                    'numExamination' =>  $numExamination,
                    'timeDuration' => $timeDuration,
                    'timeStart' => $timeStart,
                    'countLimit' => $countLimit,
                    'isPublished' => $isPublished,
                    'structureExam' =>  json_encode($structureExam),
                    'categoryId' => $categoryId,
                    'creatorId' => Auth::id(),
                ]);

                foreach ($quizList as $quiz) {
                    $content = $quiz['content'];
                    $level = $quiz['level'];
                    $top_question_ids = json_encode($quiz['top_question_ids']);
                    $bottom_question_ids = json_encode($quiz['bottom_question_ids']);

                    $question = Question::create([
                        'content' => $content,
                        'level' => $level,
                        'top_question_ids' => $top_question_ids,
                        'bottom_question_ids' => $bottom_question_ids
                    ]);

                    $questionBank_questions = QuestionBank_Questions::create([
                        'questionBankId' => $questionBank['id'],
                        'questionId' => $question['id']
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
                    $this->setStatus('200');
                    $this->setMessage("Creat Exam is successfully !");
                } else {
                    $this->setStatus('400');
                    $this->setMessage("Creat Exam is fail !");
                }
            } else {
                $this->setStatus('400');
                $this->setMessage("Name of exam is existed!");
            }
        }
        return $this->respond();
    }

    public function adddQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $quizList = $validated_request['questionList'];
        $userId = auth()->id();

        $checkQuestionBank = QuestionBank::where(['id' => $questionBankId, 'creatorId' => $userId])->first();
        if ($checkQuestionBank) {
            foreach ($quizList as $quiz) {
                $content = $quiz['content'];
                $level = $quiz['level'];
                $top_question_ids = json_encode($quiz['top_question_ids']);
                $bottom_question_ids = json_encode($quiz['bottom_question_ids']);

                $question = Question::create([
                    'content' => $content,
                    'level' => $level,
                    'top_question_ids' => $top_question_ids,
                    'bottom_question_ids' => $bottom_question_ids
                ]);

                $questionBank_questions = QuestionBank_Questions::create([
                    'questionBankId' => $questionBankId,
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

            $this->setStatus('200');
            $this->setMessage("Add new questions is successfully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Add new questions is failed !");
        }
        return $this->respond();
    }

    public function updateQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $userId = auth()->id();
        $timeStart = Carbon::createFromTimestamp($validated_request['timeStart'])->toDateTimeString();

        if (!empty($validated_request['name'])) {
            $name_questionBank = Str::lower($validated_request['name']);
            $questionBankExitId = QuestionBank::where(['creatorId' => $userId, 'name' => $name_questionBank])->pluck('id')->toArray();
            if ($questionBankExitId[0] == $questionBankId) {
                $questionBank = QuestionBank::where('id', $questionBankId)->update($request->all());
                if (!empty($validated_request['timeStart'])) {
                    $questionBank = QuestionBank::where('id', $questionBankId)->update([
                        "timeStart" => $timeStart
                    ]);
                }
                $this->setData(new QuestionBankResource(QuestionBank::findOrFail($questionBankId)));
                $this->setStatus('200');
                $this->setMessage("Update question bank successfully.");
            } else {
                $this->setStatus('400');
                $this->setMessage("Name question bank is existed");
            }
        } else {
            $questionBank = QuestionBank::where('id', $questionBankId)->update($request->all());
            if (!empty($validated_request['timeStart'])) {
                $questionBank = QuestionBank::where('id', $questionBankId)->update([
                    "timeStart" => $timeStart
                ]);
            }
            $this->setData(new QuestionBankResource(QuestionBank::findOrFail($questionBankId)));
            $this->setStatus('200');
            $this->setMessage("Update  question bank successfully.");
        }
        return $this->respond();
    }

    public function updateQuestion(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionList = $validated_request['questionList'];
        $checkStatus = false;
        foreach ($questionList as $question) {
            if (!empty($question['question'])) {
                foreach ($question['question'] as $item) {
                    $questionId = $item['id'];
                    if (!empty($item['content'])) {
                        Question::where('id', $questionId)->update([
                            "content" => $item['content']
                        ]);
                    }
                    if (!empty($item['level'])) {
                        Question::where('id', $questionId)->update([
                            "level" => $item['level']
                        ]);
                    }
                    if (!empty($item['top_question_ids'])) {
                        Question::where('id', $questionId)->update([
                            "top_question_ids" => json_encode($item['top_question_ids'])
                        ]);
                    }
                    if (!empty($item['bottom_question_ids'])) {
                        Question::where('id', $questionId)->update([
                            "top_question_ids" => json_encode($item['top_question_ids'])
                        ]);
                    }
                }
            }

            if (!empty($question['answer'])) {
                foreach ($question['answer'] as $item) {
                    $answerId = $item['id'];
                    if (!empty($item['content'])) {
                        DetailQuestion::where('id', $answerId)->update([
                            "content" => $item['content']
                        ]);
                    }

                    if (!empty($item['isCorrect'])) {
                        // dd($item['isCorrect']);
                        DetailQuestion::where('id', $answerId)->update([
                            "isCorrect" => $item['isCorrect']
                        ]);

                        $questionId = DetailQuestion::where('id', $answerId)->pluck('questionId')->toArray();
                        $answersOtherId = DetailQuestion::where('questionId', $questionId[0])->pluck('id')->toArray();
                        foreach ($answersOtherId as $id) {
                            if ($id != $answerId) {
                                DetailQuestion::where('id', $id)->update([
                                    "isCorrect" => 0
                                ]);
                            }
                        }
                    }
                }
            }
            $checkStatus = true;
        }
        if ($checkStatus) {
            $this->setStatus('200');
            $this->setMessage("Update question successfully");
        } else {
            $this->setStatus('400');
            $this->setMessage("Update question failed");
        }
        return $this->respond();
    }

    public function activeQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBank = QuestionBank::where('id', $validated_request['id'])->where('creatorId', auth()->id())->update([
            "isPublished" => 1
        ]);

        if ($questionBank) {
            $this->setStatus('200');
            $this->setMessage("Active exam successfully!");
        } else {
            $this->setStatus('400');
            $this->setMessage("Active exam failed!");
        }

        return $this->respond();
    }

    public function deleteQuestionBank(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['id'];
        $questionBank = QuestionBank::FindOrFail($questionBankId);

        $questionsId = QuestionBank_Questions::where('questionBankId', $questionBankId)->pluck('questionId')->toArray();

        if ($questionsId) {
            $this->setStatus('400');
            $this->setMessage("Failed, you have to delete all questions belong question bank before deleting it");
        } else {
            if ($questionBank->delete()) {
                $this->setStatus('200');
                $this->setMessage("Delete successfully");
            } else {
                $this->setStatus('400');
                $this->setMessage("Delete Failed");
            }
        }
        return $this->respond();
    }

    public function deleteQuestion(QuestionBankRequests $request)
    {
        $validated_request = $request->validated();

        $questionId = $validated_request['id'];

        $question = Question::where('id', $questionId);
        $questionBank_question = QuestionBank_Questions::where('questionId', $questionId);
        $detailQuestionsId =  DetailQuestion::where(['questionId' =>  $questionId])->pluck('id')->toArray();

        if ($question && $questionBank_question &&  $detailQuestionsId) {
            $question->delete();
            $questionBank_question->delete();

            foreach ($detailQuestionsId as $detailQuestionId) {
                $detailQuestion = DetailQuestion::where('id', $detailQuestionId);
                $detailQuestion->delete();
            }

            $this->setStatus('200');
            $this->setMessage("Delete successfully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Delete failed !");
        }

        return $this->respond();
    }
}
