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
            case 'exam.getExam':
                return [
                    // 'id' => ['required', 'string', 'exists:categories,id'],
                ];
                break;
            case 'exam.createExam':
                return [
                    'categoryId' => ['required', 'string', 'exists:categories,id'],
                    'name' => ['required', 'string'],
                    'newQuizList' => ['required', 'string'],
                    'timeDuration' => ['required', 'integer'],
                    'timeStart' => ['required', 'integer'],
                    'countLimit' => ['required', 'integer'],
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
                    // 'id' => ['required', 'string', 'exists:categories,id'],
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
