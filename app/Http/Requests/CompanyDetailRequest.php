<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyDetailRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tones_incluide' => 'integer|max:19',
            'pac_id' => 'integer|max:10',
            'fechaco' => 'date',
            'fechaven' => 'date',
            'sta_prod' => 'enum:P,T',
            'certificate' => [
                'required',
                'mimes:cer'
            ],
            'private_key' => [
                'required',
                'mimes:key'
            ],
            'password_key' => 'nullable|string|max:255',
            'contcert' => 'nullable|string|max:255',
            'expiration_date_cert' => 'nullable|date',
            'start_date_cert' => 'nullable|date',
            'company_id' => 'string|max:36',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }


    public function messages()
    {
        return [
            // 'tones_incluide.required' => 'Los timbres incluidos son requeridos',
            'tones_incluide.max' => 'Los timbres incluidos no pueden tener',
            // 'pac_id.required' => 'El id es requerido',
            // 'fechaco.required' => 'La fecha de compra es requerida',
            // 'fechaven.required' => 'La fecha de vencimiento es requerida',
            // 'sta_prod.required' => 'El estado es requerido',
            'sta_prod.enum' => 'El estado debe ser P o T',
            'certificate.mimes' => 'El certificado debe ser un archivo con extensión cer',
            'private_key.mimes' => 'La clave privada debe ser un archivo con extensión key',
            'password_key.string' => 'La contraseña de clave privada debe ser una cadena',
            'contcert.string' => 'El contenido del certificado debe ser una cadena',
            'expiration_date_cert.date' => 'La fecha de expiración del certificado debe ser una fecha valida',
            'start_date_cert.date' => 'La fecha de inicio del certificado debe ser una fecha valida',
            // 'company_id.required' => 'El id de la empresa es requerido',
        ];
    }
}
