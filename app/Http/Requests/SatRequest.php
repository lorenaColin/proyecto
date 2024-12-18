<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('get') && $this->path() === 'api/searchCodePostal') {
            return [
                'cp' => ['required','string', 'min:5', 'max:5'],
            ];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => true,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }


    public function messages()
    {
        return [
            'cp.required' => 'El código postal es requerido.',
            'cp.string' => 'El nombre debe ser una cadena.',
            'cp.min' => 'El código postal tiene que tener mínimo 5 caracteres.',
            'cp.max' => 'El código postal tiene que tener máximo 5 caracteres.',
        
        ];

    }
}
