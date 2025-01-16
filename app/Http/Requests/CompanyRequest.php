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
            'name' => ['required', 'string', 'min:5', 'max:254'],
            'address' => ['string', 'nullable'],
            'cp' => ['required', 'string', 'min:5', 'max:5'],
            'curp' => ['string', 'nullable', 'min:18', 'max:18'],
            'status' =>['string', 'in:Activo,Inactivo'],
            'rfc' => ['required', 'string', 'min:12', 'max:13'],
            'regime' => ['required', 'string', 'min:3', 'max:3'],
            'employee_registration' => ['string', 'nullable', 'min:1', 'max:20'],
            'email'=>['required', 'email', 'string', 'max:75'],
            'phone'=>['required', 'string', 'min:10', 'max:13'],
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
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena.',
            'name.min' => 'El nombre tiene que tener mínimo 5 caracteres.',
            'name.max' => 'El nombre tiene que tener máximo 254 caracteres.',
            'address.string' => 'El nombre debe ser una cadena.',
            'cp.required' => 'El código postal es requerido.',
            'cp.string' => 'El código postal debe ser una cadena.',
            'cp.min' => 'El código postal tiene que tener mínimo 5 caracteres.',
            'cp.max' => 'El código postal tiene que tener máximo 5 caracteres.',
            'curp.string' => 'El curp debe ser una cadena.',
            'curp.min' => 'El curp tiene que tener mínimo 18 caracteres.',
            'curp.max' => 'El curp tiene que tener máximo 18 caracteres.',
            'status.string' => 'El status debe ser una cadena.',
            'status.in' => 'El status deben ser "Activo" o "Inactivo".',
            'rfc.required' => 'El rfc es requerido.',
            'rfc.string' => 'El rfc debe ser una cadena.',
            'rfc.min' => 'El rfc tiene que tener mínimo 12 caracteres.',
            'rfc.max' => 'El rfc tiene que tener máximo 13 caracteres.',
            'regime.required' => 'El régimen es requerido.',
            'regime.string' => 'El régimen debe ser una cadena.',
            'regime.min' => 'El régimen tiene que tener mínimo 3 caracteres.',
            'regime.max' => 'El régimen tiene que tener máximo 3 caracteres.',
            'employee_registration.string' => 'El registro patronal debe ser una cadena.',
            'employee_registration.min' => 'El registro patronal tiene que tener mínimo 1 caracter.',
            'employee_registration.max' => 'El registro patronal tiene que tener máximo 20 caracteres.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.string' => 'El correo electrónico debe ser una cadena.',
            'email.max' => 'El correo electrónico tiene que tener máximo 75 caracteres.',
            'phone.required' => 'El teléfono es requerido.',
            'phone.string' => 'El teléfono debe ser una cadena.',
            'phone.min' => 'El teléfono tiene que tener mínimo 10 caracteres.',
            'phone.max' => 'El teléfono tiene que tener máximo 13 caracteres.',
        ];
    }
}
