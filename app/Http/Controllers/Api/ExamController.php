<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamRequests;
use App\Http\Resources\DetailQuestionResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\DetailQuestion;
use App\Models\Exams;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionBank_Questions;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ExamController extends AbstractApiController
{
    public function getExams()
    {
        $examsId = Exams::where('creatorId', auth()->id())->orderBy('created_at', 'DESC')->pluck('id')->toArray();
        $data = array();

        foreach ($examsId as $examId) {
            $exam['main'] = ExamResource::collection(Exams::where('creatorId', auth()->id())->where('id', $examId)->get());
            $questionsId = Exams::where('creatorId', auth()->id())->where('id', $examId)->pluck('arrayQuestion')->toArray();
            $questionsId = json_decode($questionsId[0], true);

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

            $exam['sub'] =  $totalQuestion;
            array_push($data, $exam);
        }

        $this->setData($data);
        $this->setStatus('200');
        $this->setMessage("List all exams");

        return $this->respond();
    }

    public function getDetailExam(ExamRequests $request)
    {
        $validated_request = $request->validated();
        $examId = $validated_request['id'];

        $data_question = array();
        $data['general']['main'] = ExamResource::collection(Exams::where('creatorId', auth()->id())->where('id', $examId)->get());

        $questionsId = Exams::where('creatorId', auth()->id())->where('id', $examId)->pluck('arrayQuestion')->toArray();

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
        return $this->respond();
    }

    public function createExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['questionBankId'];
        $name = Str::lower($validated_request['name']);
        $structureExam = $validated_request['structureExam'];
        $timeDuration = $validated_request['timeDuration'];
        $timeStart = Carbon::createFromTimestamp($validated_request['timeStart'])->toDateTimeString();
        $countLimit = $validated_request['countLimit'];
        $note = $validated_request['note'] ?? null;
        $isPublished = $validated_request['isPublished'] ?? 0;
        $numExams = $validated_request['numExams'] ?? 1;

        $numEasy = $structureExam['easy'];
        $numNormal = $structureExam['normal'];
        $numDifficult = $structureExam['difficult'];
        $esay = 1;
        $normal = 2;
        $difficult = 3;
        $arrayQuestionsEasy = $this->getQuestionsId($questionBankId, $esay);
        $arrayQuestionsNormal = $this->getQuestionsId($questionBankId, $normal);
        $arrayQuestionsDifficult = $this->getQuestionsId($questionBankId, $difficult);
        $exams = [];
        while ($numExams > 0) {
            $numExamination = rand(0, 999999);
            $statusNumExamination = Exams::where('numExamination', $numExamination)->get();
            while (!$statusNumExamination) {
                $numExamination = rand(0, 999999);
                $statusNumExamination = Exams::where('numExamination', $numExamination)->get();
            }

            $randomQuestionEeasy = $this->randomQuestion($arrayQuestionsEasy, $numEasy);
            $randomQuestionNormal = $this->randomQuestion($arrayQuestionsNormal, $numNormal);
            $randomQuestionDifficult = $this->randomQuestion($arrayQuestionsDifficult, $numDifficult);

            $arrayQuestionsId = [];
            if (is_array($randomQuestionEeasy) == true) {
                foreach ($randomQuestionEeasy as $question) {
                    array_push($arrayQuestionsId, $arrayQuestionsEasy[$question]);
                }
            } else {
                array_push($arrayQuestionsId, $arrayQuestionsEasy[$randomQuestionEeasy]);
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

            $exam = Exams::create([
                'name' => $name,
                'arrayQuestion' => json_encode($arrayQuestionsId),
                'timeDuration' => $timeDuration,
                'timeStart' => $timeStart,
                'countLimit' => $countLimit,
                'note' => $note,
                'numExamination' =>  $numExamination,
                'isPublished' => $isPublished,
                'questionBankId' => $questionBankId,
                'creatorId' => Auth::id(),
            ]);
            array_push($exams, $exam);
            $numExams--;
        }

        if ($exams != []) {
            $this->setData(ExamResource::collection($exams));
            $this->setStatus('200');
            $this->setMessage("Creat Exam is successfully !");
        } else {
            $this->setStatus('400');
            $this->setMessage("Creat Exam is fail !");
        }

        return $this->respond();
    }

    public function remakeExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $examId = $validated_request['id'];
        $structureExam = $validated_request['structureExam'];
        $questionBankId = Exams::where('id', $examId)->pluck('questionBankId')->toArray();
        $resultsId = Result::where('examId', $examId)->pluck('id')->toArray();

        if ($resultsId) {
            $this->setStatus('400');
            $this->setMessage("Failed, the exam has results of user");
        } else {
            $numEasy = $structureExam['easy'];
            $numNormal = $structureExam['normal'];
            $numDifficult = $structureExam['difficult'];
            $esay = 1;
            $normal = 2;
            $difficult = 3;
            $arrayQuestionsEasy = $this->getQuestionsId($questionBankId[0], $esay);
            $arrayQuestionsNormal = $this->getQuestionsId($questionBankId[0], $normal);
            $arrayQuestionsDifficult = $this->getQuestionsId($questionBankId[0], $difficult);

            $randomQuestionEeasy = $this->randomQuestion($arrayQuestionsEasy, $numEasy);
            $randomQuestionNormal = $this->randomQuestion($arrayQuestionsNormal, $numNormal);
            $randomQuestionDifficult = $this->randomQuestion($arrayQuestionsDifficult, $numDifficult);

            $arrayQuestionsId = [];
            if (is_array($randomQuestionEeasy) == true) {
                foreach ($randomQuestionEeasy as $question) {
                    array_push($arrayQuestionsId, $arrayQuestionsEasy[$question]);
                }
            } else {
                array_push($arrayQuestionsId, $arrayQuestionsEasy[$randomQuestionEeasy]);
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

            $status = Exams::where('id', $examId)->update([
                'arrayQuestion' => json_encode($arrayQuestionsId),
            ]);

            if ($status) {
                $exam =  Exams::where('id', $examId)->get();
                $this->setData(ExamResource::collection($exam));
                $this->setStatus('200');
                $this->setMessage("Remake exam is successfully !");
            } else {
                $this->setStatus('400');
                $this->setMessage("Remake exam is fail !");
            }
        }
        return $this->respond();
    }

    public function updateExam(ExamRequests $request)
    {
    }

    public function deleteExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $examId = $validated_request['id'];
        $exam = Exams::FindOrFail($examId);

        $resultId = Result::where(['examId' =>  $examId])->pluck('id')->toArray();

        if ($resultId) {
            $this->setStatus('400');
            $this->setMessage("Failed, you have to delete question bank before deleting a category!");
        } else {
            if ($exam->delete()) {
                $this->setStatus('200');
                $this->setMessage("Delete successfully");
            } else {
                $this->setStatus('400');
                $this->setMessage("Delete Failed");
            }
        }
        return $this->respond();
    }

    public function activeExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $exam = Exams::where('id', $validated_request['id'])->where('creatorId', auth()->id())->update([
            "isPublished" => 1
        ]);

        if ($exam) {
            $this->setStatus('200');
            $this->setMessage("Active exam successfully!");
        } else {
            $this->setStatus('400');
            $this->setMessage("Active exam failed!");
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

        // $timeStart = Carbon::createFromTimestamp($validated_request['timeStart'])->toDateTimeString();
                    // if (!empty($validated_request['timeStart'])) {
                    //     $questionBank = QuestionBank::where('id', $questionBankId)->update([
                    //         "timeStart" => $timeStart
                    //     ]);
                    // }
                    // if (!empty($validated_request['timeStart'])) {
                    //     $questionBank = QuestionBank::where('id', $questionBankId)->update([
                    //         "timeStart" => $timeStart
                    //     ]);
                    // }