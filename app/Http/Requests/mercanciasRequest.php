<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class mercanciasRequest extends FormRequest
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
            'claveProdServCP' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'claveUnidad' => ['required', 'string', 'max:255'],
            'unidad' => ['required', 'string', 'max:255'],
            'dimensiones' => ['nullable', 'string', 'max:255'],
            'materialPeligroso' => ['nullable', 'boolean'],
            'cveMaterialPeligroso' => ['nullable', 'string', 'max:255'],
            'embalaje' => ['nullable', 'string', 'max:255'],
            'descripEmbalaje' => ['nullable', 'string', 'max:255'],
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
            'claveProdServCP.required' => 'La clave del producto o servicio es obligatoria.',
            'claveProdServCP.string' => 'La clave del producto o servicio debe ser una cadena de texto.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'claveUnidad.required' => 'La clave de unidad es obligatoria.',
            'claveUnidad.string' => 'La clave de unidad debe ser una cadena de texto.',
            'unidad.required' => 'La unidad es obligatoria.',
            'unidad.string' => 'La unidad debe ser una cadena de texto.',
            'dimensiones.string' => 'Las dimensiones deben ser una cadena de texto.',
            'materialPeligroso.boolean' => 'El campo de material peligroso debe ser verdadero o falso.',
            'cveMaterialPeligroso.string' => 'La clave del material peligroso debe ser una cadena de texto.',
            'embalaje.string' => 'El embalaje debe ser una cadena de texto.',
            'descripEmbalaje.string' => 'La descripción del embalaje debe ser una cadena de texto.',
            'uuid_company.required' => 'El UUID de la compañía es obligatorio.',
            'uuid_company.uuid' => 'El UUID de la compañía debe tener un formato válido.',
        ];
    }
}
