<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class SerieRequest extends FormRequest
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
            'serie' => ['required', 'string', 'max:25'],
            'folio' => ['required', 'integer', 'min:5'],
            'tipoComprobante' => ['required', 'string', 'max:40'],
            'uuid_company' => ['required', 'uuid', 'exists:companies,id'],
            'status' => ['required', 'boolean'],
        ];
    }
    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'error'   => false,
    //         'message'   => 'Validation errors',
    //         'data'      => $validator->errors()
    //     ]));
    // }
    public function messages()
{
    return [
        'serie.required' => 'La serie es requerida.',
        'serie.numeric' => 'La serie debe ser un valor numérico.',
        'serie.digits' => 'La serie debe tener exactamente 25 carcteres.',
        
        'folio.required' => 'El folio es requerido.',
        'folio.string' => 'El folio debe ser una entero.',
        'folio.max' => 'El folio no puede tener minimo de 5 digitos.',
        
        'tipoComprobante.required' => 'El tipo de comprobante es requerido.',
        'tipoComprobante.string' => 'El tipo de comprobante debe ser una cadena.',
        'tipoComprobante.max' => 'El tipo de comprobante no puede tener más de 40 caracteres.',
        
        'uuid_company.required' => 'El UUID de la empresa es requerido.',
        'uuid_company.uuid' => 'El UUID de la empresa debe ser válido.',
        'uuid_company.exists' => 'La empresa especificada no existe en la base de datos.',
        
        'status.required' => 'El estado debe ser requerdio',
    ];
}

}
