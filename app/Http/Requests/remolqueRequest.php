<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class remolqueRequest extends FormRequest
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
        return [
            'SubTipoRem' => ['required', 'string'],
            'placa' => ['required', 'string'],
            'uuid_company' => ['required', 'uuid', 'exists:companies,id'],
        ];
        
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => true,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));
    }
    public function messages()
    {
        return [
            'SubTipoRem.required' => 'El tipo de remolque  es obligatorio.',
            'SubTipoRem.string' => 'El tipo de remolque debe ser una cadena de caracteres.',
            'placa.required' => 'La placa es obligatorio.',
            'placa.string' => 'La placa debe ser una cadena de caracteres.',
            'uuid_company.required' => 'El UUID de la empresa es requerido.',
            'uuid_company.uuid' => 'El UUID de la empresa debe ser válido.',
            'uuid_company.exists' => 'La empresa especificada no existe en la base de datos.',
        ];
    }
}
