<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionRequests extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $name = request()->route()->getName();
        $id = request()->route('id');
        switch ($name) {
            // case 'question.getQuestion':
            //     return [
            //         'id' => ['required', 'string', 'exists:questions,id'],
            //     ];
            //     break;
            case 'question.getQuestionsByExamId':
                return [
                    'examId' => ['required', 'string', 'exists:exams,id'],
                ];
                break;
            case 'question.updateQuestion':
                return [
                    'id' => ['required', 'string', 'exists:categories,id'],
                    'name' => 'string|required',
                ];
                break;
            case 'question.deleteQuestion':
                return [
                    'id' => ['required', 'string', 'exists:questions,id'],
                ];
                break;
            default:
                return [];
                break;
        }
    }

    protected function prepareForValidation()
    {
        $this->merge(['id' => $this->route('id')]);
        $this->merge(['examId' => $this->route('examId')]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => false
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
