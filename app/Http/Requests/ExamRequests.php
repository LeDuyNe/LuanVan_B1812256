<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamRequests extends FormRequest
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
            case 'exam.getDetailExam':
                return [
                    'id' => ['required', 'string', 'exists:exams,id'],
                ];
                break;
            case 'exam.createExam':
                return [
                    'questionBankId' => ['required', 'string', 'exists:questionbank,id'],
                    'name' => ['required', 'string'],
                    'questionList' => ['required', 'array'],
                    'timeDuration' => ['required', 'integer'],
                    'timeStart' => ['required', 'integer'],
                    'countLimit' => ['required', 'integer'],
                    'note' => ['string', 'nullable'],
                    'isPublished' => ['boolean', 'nullable'],
                ];
                break;
            case 'exam.activeExam':
                return [
                    'id' => ['required', 'string', 'exists:exams,id'],
                ];
                break;
            case 'exam.updateExam':
                return [
                    // 'id' => ['required', 'string', 'exists:categories,id'],
                    // 'name' => 'string|required',
                ];
                break;
            case 'exam.deleteExam':
                return [
                    'id' => ['required', 'string', 'exists:exams,id'],
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
