<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionBankRequests extends FormRequest
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
            case 'questionbank.getDetailQuestionBank':
                return [
                    'id' => ['required', 'string', 'exists:questionbank,id'],
                ];
                break;
            case 'questionbank.addQuestionBank':
                return [
                    'id' => ['required', 'string', 'exists:questionbank,id'],
                    'questionList' => ['required', 'array'],
                ];
                break;
            case 'questionbank.activeQuestionBank':
                return [
                    'id' => ['required', 'string', 'exists:questionbank,id'],
                ];
                break;
            case 'questionbank.createQuestionBank':
                return [
                    'categoryId' => ['required', 'string', 'exists:categories,id'],
                    'name' => ['required', 'string'],
                    'questionList' => ['required', 'array'],
                    'note' => ['string', 'nullable'],
                    'timeDuration' => ['required', 'integer'],
                    'timeStart' => ['required', 'integer'],
                    'countLimit' => ['required', 'integer'],
                    'note' => ['string', 'nullable'],
                    'structureExam' => ['required', 'array'],
                    'isPublished' => ['boolean', 'nullable'],
                ];
                break;
            case 'questionbank.updateQuestionBank':
                return [
                    'id' => ['required', 'string', 'exists:questionbank,id'],
                    'timeStart' => ['nullable', 'integer'],
                    'name' => ['nullable', 'string'],
                    'timeDuration' => ['nullable', 'integer'],
                    'countLimit' => ['nullable', 'integer'],
                    'note' => ['nullable', 'string'],
                    'structureExam' => ['nullable', 'array'],
                    'isPublished' => ['nullable', 'boolean'],
                ];
                break;
            case 'questionbank.updateQuestion':
                return [
                    'questionList' => ['required', 'array'],
                ];
                break;
            case 'questionbank.deleteQuestionBank':
                return [
                    'id' => ['required', 'string', 'exists:questionbank,id'],
                ];
                break;
            case 'questionbank.deleteQuestion':
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
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => false
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
