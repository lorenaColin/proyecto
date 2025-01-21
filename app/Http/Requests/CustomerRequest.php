<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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

            'rfc' => ['required', 'string', 'min:12', 'max:13', Rule::unique('customers', 'rfc')->ignore($this->route('customer'))],
            'name' => ['required', 'string', 'min:5', 'max:254'],
            'cp' => ['nullable', 'min:5', 'max:5'],
            'residence' => ['nullable', 'min:3', 'max:3'],
            'num_reg_id_trib' => ['nullable', 'min:1', 'max:40'],
            'regime' => ['required', 'string', 'min:3', 'max:3'],
            'address' => ['string'],
            'email'=>['string','email', 'max:75'],
            'phone'=>['string', 'min:10', 'max:13'],
            'status' =>['string', 'in:Activo,Inactivo'],
            'payment_form' =>['string', 'nullable'],
            'payment_method'=>['string', 'nullable'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
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

            'rfc.required' => 'El rfc es requerido.',
            'rfc.string' => 'El rfc debe ser una cadena.',
            'rfc.min' => 'El rfc tiene que tener mínimo 12 caracteres.',
            'rfc.max' => 'El rfc tiene que tener máximo 13 caracteres.',
            'rfc.unique' => 'El rfc ya esta en uso.',
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena.',
            'name.min' => 'El nombre tiene que tener mínimo 5 caracteres.',
            'name.max' => 'El nombre tiene que tener máximo 254 caracteres.',
            'cp.string' => 'El código postal debe ser una cadena.',
            'cp.min' => 'El código postal tiene que tener mínimo 5 caracteres.',
            'cp.max' => 'El código postal tiene que tener máximo 5 caracteres.',
            'residence.string' => 'El país debe ser una cadena.',
            'residence.min' => 'El país tiene que tener mínimo 3 caracteres.',
            'residence.max' => 'El país tiene que tener máximo 3 caracteres.',
            'num_reg_id_trib.string' => 'El NumRegIdTrib debe ser una cadena.',
            'num_reg_id_trib.min' => 'El NumRegIdTrib tiene que tener mínimo 1 caracter.',
            'num_reg_id_trib.max' => 'El NumRegIdTrib tiene que tener máximo 40 caracteres.',
            'regime.required' => 'El régimen es requerido.',
            'regime.string' => 'El régimen debe ser una cadena.',
            'regime.min' => 'El régimen tiene que tener mínimo 3 caracteres.',
            'regime.max' => 'El régimen tiene que tener máximo 3 caracteres.',
            'address.string' => 'El nombre debe ser una cadena.',
            'email.string' => 'El correo electrónico debe ser una cadena.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo electrónico tiene que tener máximo 75 caracteres.',
            'phone.string' => 'El teléfono debe ser una cadena.',
            'phone.min' => 'El teléfono tiene que tener mínimo 10 caracteres.',
            'phone.max' => 'El teléfono tiene que tener máximo 13 caracteres.',
            'status.string' => 'El status debe ser una cadena.',
            'status.in' => 'El status deben ser "Activo" o "Inactivo".',
            'company_id.required' => 'La empresa es requerida.',
            'company_id.string' => 'La empresa debe ser una cadena.',
            'company_id.exists' => 'La empresa no existe.',
        ];

    }
}
