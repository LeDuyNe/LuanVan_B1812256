<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthorizationRequests extends FormRequest
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
            case 'login':
                return [
                    'email' => 'string|email|required',
                    'password' => 'string|min:6|required',
                ];
                break;
            case 'register':
                return [
                    'name' => 'string|required',
                    'email' => 'unique:users,email|string|email|required',
                    'password' => 'string|min:6|required',
                    'avatar' => 'string|nullable',
                    'nameTitle' => 'string|nullable',
                    'role' => 'integer|between:1,2|required',
                ];
                break;
            case 'update-password':
                return [
                    'oldPassword' => 'string|min:6|required',
                    'newPassword' => 'string|min:6|required',
                ];
                break;
            case 'update-info':
                return [
                    'name' => 'string|nullable',
                    'avatar' => 'string|nullable',
                    'nameTitle' => 'string|nullable'
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
