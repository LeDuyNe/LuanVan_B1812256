<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamineesRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $name = request()->route()->getName();
        $id = request()->route('id');
        switch ($name) {
            case 'examinees.getExam':
                return [
                    'id' => ['required', 'integer', 'exists:exams,numExamination'],
                ];
                break;
            case 'examinees.getDetailResult':
                return [
                        'id' => ['required', 'string', 'exists:result,id'],
                ];
                    break;
            case 'examinees.submitExam':
                return [
                    'examId' => ['required', 'string', 'exists:exams,id'],
                    'restTime' => ['nullable', 'integer'],
                    'answerIds' => ['required', 'array'],
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
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => false
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
