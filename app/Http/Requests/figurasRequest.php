<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class figurasRequest extends FormRequest
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
            'rfcFigura' => ['nullable', 'string',],
            'tipoFigura' => ['required', 'string',],
            'numLicencia' => ['nullable', 'string', 'min:6', 'max:16'],
            'nombreFigura' => ['nullable', 'string', 'max:254'],
            'numRegIdTribFigura' => ['nullable', 'string', 'min:6', 'max:40'],
            'residenciaFiscalFigura' => ['nullable', 'string'],
            'domicilio' => ['nullable', 'string'],
            'pais' => ['nullable', 'string'],
            'codigoPostal' => ['nullable', 'string', 'min:1', 'max:12'],
            'estado' => ['nullable', 'string', 'min:1', 'max:30'],
            'municipio' => ['nullable', 'string'],
            'localidad' => ['nullable', 'string'],
            'colonia' => ['nullable', 'string'],
            'calle' => ['nullable', 'string'],
            'numeroExterior' => ['nullable', 'string'],
            'numeroInterior' => ['nullable', 'string'],
            'referencia' => ['nullable', 'string'],
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
         
            'rfcFigura.string' => 'El RFC de la figura debe ser una cadena de caracteres.',

            'tipoFigura.required' => 'El tipo de figura es obligatorio.',
            'tipoFigura.string' => 'El tipo de figura debe ser una cadena de caracteres.',

            'numLicencia.string' => 'El número de licencia debe ser una cadena de caracteres.',
            'numLicencia.min' => 'El número de licencia debe tener al menos 6 caracteres.',
            'numLicencia.max' => 'El número de licencia no debe tener más de 16 caracteres.',

            'nombreFigura.string' => 'El nombre de la figura debe ser una cadena de caracteres.',
            'nombreFigura.max' => 'El nombre de la figura no debe tener más de 254 caracteres.',

            'numRegIdTribFigura.string' => 'El número de registro tributario debe ser una cadena de caracteres.',
            'numRegIdTribFigura.min' => 'El número de registro tributario debe tener al menos 6 caracteres.',
            'numRegIdTribFigura.max' => 'El número de registro tributario no debe tener más de 40 caracteres.',

            'residenciaFiscalFigura.string' => 'La residencia fiscal debe ser una cadena de caracteres.',
            'domicilio.string' => 'El domicilio debe ser una cadena de caracteres.',
            'pais.string' => 'El país debe ser una cadena de caracteres.',
            'codigoPostal.string' => 'El código postal debe ser una cadena de caracteres.',
            'codigoPostal.min' => 'El código postal debe tener al menos 1 carácter.',
            'codigoPostal.max' => 'El código postal no debe tener más de 12 caracteres.',
            'estado.string' => 'El estado debe ser una cadena de caracteres.',
            'estado.min' => 'El estado debe tener al menos 1 carácter.',
            'estado.max' => 'El estado no debe tener más de 30 caracteres.',
            'municipio.string' => 'El municipio debe ser una cadena de caracteres.',
            'localidad.string' => 'La localidad debe ser una cadena de caracteres.',
            'colonia.string' => 'La colonia debe ser una cadena de caracteres.',
            'calle.string' => 'La calle debe ser una cadena de caracteres.',
            'numeroExterior.string' => 'El número exterior debe ser una cadena de caracteres.',
            'numeroInterior.string' => 'El número interior debe ser una cadena de caracteres.',
            'referencia.string' => 'La referencia debe ser una cadena de caracteres.',

            'uuid_company.required' => 'El UUID de la empresa es requerido.',
            'uuid_company.uuid' => 'El UUID de la empresa debe ser válido.',
            'uuid_company.exists' => 'La empresa especificada no existe en la base de datos.',
        ];
    }
}
