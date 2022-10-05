<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractApiController;
use App\Http\Requests\ExamRequests;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Models\Category;
use App\Models\Exams;
use App\Models\Question;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ExamController extends AbstractApiController
{
    public function getExams()
    {
        // $exams = ExamResource::collection(Exams::where('creatorId', auth()->id())->get());
        $examsId = Exams::where('creatorId', auth()->id())->get('id');

        $data = array();
        foreach ($examsId as $examId) {
            $exam['general'] = ExamResource::collection(Exams::where('id', $examId['id'])->get());
            $exam['questions'] = QuestionResource::collection(Question::where('examId', $examId['id'])->get());
            array_push($data, $exam);
        }

        $this->setData($data);
        $this->setStatus('200');
        $this->setMessage("List all exams");

        return $this->respond($examId);
    }

    public function getDetailExam(ExamRequests $request)
    {
        $validated_request = $request->validated();
        $examId = $validated_request['id'];

        $exam = ExamResource::collection(Exams::where('id', $examId)->get());
        $questions = QuestionResource::collection(Question::where('examId', $examId)->get());

        $data['info'] = $exam;
        $data['questions'] = $questions;

        $this->setData($data);
        $this->setStatus('200');
        $this->setMessage("Succefully !");
        return $this->respond();
    }

    public function createExam(ExamRequests $request)
    {
        $validated_request = $request->validated();

        $questionBankId = $validated_request['questionBankId'];
        $name = Str::lower($validated_request['name']);
        $newQuizList = $validated_request['newQuizList'];
        $timeDuration = $validated_request['timeDuration'];
        $timeStart =  gmdate("Y-m-d H:i:s", $validated_request['timeStart']);
        $countLimit = $validated_request['countLimit'];
        $note = NULL;
        $isPublished = 0;
        $userId = auth()->id();

        if (!empty($validated_request['note'])) {
            $note = $validated_request['note'];
        }

        if (!empty($validated_request['isPublished'])) {
            $isPublished = $validated_request['isPublished'];
        }

        // $checkActiveCategory = Category::where(['id' => $categoryId, 'isPublished' => 1])->first();
        // if(!$checkActiveCategory){
        //     $this->setMessage("The category must be activated!");
        //     return $this->respond();

        // }else{
        $checkNameExam = Exams::where(['creatorId' => $userId, 'name' => $name])->first();
        if (!$checkNameExam) {
            $arrayId = $this->getQuestionId($questionBankId, 2);

            // $exam = Exams::create([
            //     'name' => $name_exam,
            //     'timeDuration' => $timeDuration,
            //     'timeStart' => $timeStart,
            //     'countLimit' => $countLimit,
            //     'note' => $note,
            //     'isPublished' => $isPublished,
            //     'categoryId' => $categoryId,
            //     'creatorId' => Auth::id(),

            // ]);

            // foreach ($newQuizList as $quiz) {
            //     $content = $quiz['content'];
            //     $correctAnswer = $quiz['correctAnswer'];
            //     $inCorrectAnswer = $quiz['inCorrectAnswer'];
            //     $level = $quiz['level'];

            //     $question = Question::create([
            //         'content' => $content,
            //         'correctAnswer' => $correctAnswer,
            //         'inCorrectAnswer' => json_encode($inCorrectAnswer),
            //         'level' => $level,
            //         'examId' => $exam['id'],
            //     ]);
            // }
            //         if ($question) {
            //             $this->setData($exam);
            //             $this->setMessage("Creat Exam is successfully !");
            //             return $this->respond();
            //         } else {
            //             $this->setMessage("Creat Exam is fail !");
            //             return $this->respond();
            //         }
            //     } else {
            //         $this->setMessage("Name of exam is existed!");
            $this->setData($arrayId);
            return $this->respond();
            //     }
            // }
        }
    }

    public function updateCategory(ExamRequests $request)
    {
        // $validated_request = $request->validated();
        // $name_category = Str::lower($validated_request['name']);
        // $userId = auth()->id();

        // $checkCategory = Category::where(['creatorId' => $userId, 'name' => $name_category])->first();

        // if (!$checkCategory) {
        //     $category = Category::where('id', $validated_request['id'])->update($request->all());

        //     $this->setStatus('200');
        //     $this->setMessage("Update category successfully.");

        //     return $this->respond();
        // }
        // $this->setStatus('400');
        // $this->setMessage("Category is existed");

        // return $this->respond();
    }


    // public function deleteExam(ExamRequests $request)
    // {
    //     $validated_request = $request->validated();

    //     $exam = Exams::FindOrFail($validated_request['id']);

    //     if ($exam->delete()) {
    //         $this->setStatus('200');
    //         $this->setMessage("Delete successfully");

    //         return $this->respond();
    //     }
    //     $this->setMessage("Delete Failed");

    //     return $this->respond();
    // }

    // public function activeExam(ExamRequests $request)
    // {
    //     $validated_request = $request->validated();

    //     $exam = Exams::where('id', $validated_request['id'])->where('creatorId', auth()->id())->update([
    //         "isPublished" => 1
    //     ]);

    //     if ($exam) {
    //         $this->setStatus('200');
    //         $this->setMessage("Active exam successfully!");

    //         return $this->respond();
    //     }
    //     $this->setStatus('400');
    //     $this->setMessage("Active exam failed!");

    //     return $this->respond();
    // }

    public function getQuestionId($id, $level){
        $questionId =Question::where('questionBankId', $id)->where('level', $level)->get('id');
        return $questionId;
    }

    public function randomQuestion($arrayId, $num){

    }
}
