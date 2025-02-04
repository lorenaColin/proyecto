<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class InsuranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'asegure' => 'required|string|max:50',
            'polize' => 'required|string|max:30',
            'company_id' => 'required|string|max:36',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error'   => true,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }


    public function messages()
    {
        return [
            'type.required' => 'El tipo de seguro es requerido',
            'asegure.required' => 'La aseguradora es requerida',
            'polize.required' => 'El número de póliza es requerido',
            'company_id.required' => 'La compañia es obligatoria',
        ];
    }
}
