<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CompanyRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'address' => ['string'],
            'colony' =>['string', 'max:100'],
            'municipality' => ['required','string', 'min:3', 'max:3'],
            'cp' => ['required', 'string', 'min:5', 'max:5'],
            'curp' => ['string', 'min:18', 'max:18'],
            'status' =>['string', 'in:Activo,Inactivo'],
            'rfc' => ['required', 'string', 'min:12', 'max:13'],
            'state' => ['required', 'string', 'min:3', 'max:3'],
            'locality' => ['string', 'min:3', 'max:3'],
            'regime' => ['required', 'string', 'min:3', 'max:3'],
            'employee_registration' => ['string','min:1', 'max:20'],
            'email'=>['required', 'email', 'string', 'max:75'],
            'phone'=>['required', 'string', 'min:10', 'max:13'],
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
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena.',
            'name.min' => 'El nombre tiene que tener mínimo 5 caracteres.',
            'name.max' => 'El nombre tiene que tener máximo 255 caracteres.',
            'address.string' => 'El nombre debe ser una cadena.',
            'colony.string' => 'La colonia debe ser una cadena.',
            'colony.max' => 'La colonia tiene que tener máximo 100 caracteres.',
            'municipality.required' => 'El municipio es requerido.',
            'municipality.string' => 'El municipio debe ser una cadena.',
            'municipality.min' => 'El municipio tiene que tener mínimo 3 caracteres.',
            'municipality.max' => 'El municipio tiene que tener máximo 3 caracteres.',
            'cp.required' => 'El código postal es requerido.',
            'cp.string' => 'El código postal debe ser una cadena',
            'cp.min' => 'El código tiene que tener mínimo 5 caracteres',
            'cp.max' => 'El código tiene que tener máximo 5 caracteres',
            'curp.string' => 'El curp debe ser una cadena',
            'curp.min' => 'El curp tiene que tener mínimo 18 caracteres.',
            'curp.max' => 'El curp tiene que tener máximo 18 caracteres.',
            'rfc.required' => 'El rfc es requerido.',
            'rfc.string' => 'El rfc debe ser una cadena.',
            'rfc.min' => 'El rfc tiene que tener mínimo 12 caracteres.',
            'rfc.max' => 'El rfc tiene que tener máximo 13 caracteres.',
            'state.required' => 'El estado es requerido.',
            'state.string' => 'El estado debe ser una cadena.',
            'state.min' => 'El estado tiene que tener mínimo 3 caracteres.',
            'state.max' => 'El estado tiene que tener máximo 3 caracteres.',
            'regime.required' => 'El régimen es requerido.',
            'regime.string' => 'El régimen debe ser una cadena.',
            'regime.min' => 'El régimen tiene que tener mínimo 3 caracteres',
            'regime.max' => 'El régimen tiene que tener máximo 3 caracteres',
            'employee_registration.string' => 'El registro patronal debe ser una cadena.',
            'employee_registration.min' => 'El registro patronal tiene que tener mínimo 1 caracter.',
            'employee_registration.max' => 'El registro patronal tiene que tener máximo 20 caracter.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico no tiene un formato válido',
            'email.string' => 'El correo electrónico debe ser una cadena.',
            'email.max' => 'El correo electrónico tiene que tener máximo 75 caracteres.',
            'phone.required' => 'El telefono es requerido.',
            'phone.string' => 'El correo electrónico debe ser una cadena.',
            'phone.min' => 'El telefono tiene que tener mínimo 10 caracteres.',
            'phone.max' => 'El telefono tiene que tener máximo 13 caracteres.',
        ];
    }
}
