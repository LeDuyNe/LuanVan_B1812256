<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequests extends FormRequest
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
            case 'category.getCategorie':
                return [
                    'id' => ['required', 'string', 'exists:categories,id'],
                ];
                break;
            case 'category.createCategory':
                return [
                    'name' => ['string', 'required'],
                    'note' => ['string', 'nullable'],
                    'is_published' => ['boolean', 'nullable'],
                ];
                break;
            case 'category.updateCategory':
                return [
                    'id' => ['required', 'string', 'exists:categories,id'],
                    'name' => ['string', 'nullable'],
                    'note' => ['string', 'nullable'],
                    'is_published' => ['boolean', 'nullable'],
                ];
                break;
            case 'category.deleteCategory':
                return [
                    'id' => ['required', 'string', 'exists:categories,id'],
                ];
                break;
            case 'category.activeCategory':
                return [
                    'id' => ['required', 'string', 'exists:categories,id'],
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
