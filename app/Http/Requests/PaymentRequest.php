<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'type' => ['required','string', 'in:T,D,PE,R', 'min:1', 'max:2'],
            'description' => ['required', 'string', 'min:10', 'max:255'],
            'tones' =>['required', 'integer'],
            'gitf_tones' =>['required', 'integer'],
            'amount' => ['required','numeric'],
            'date' => ['required', 'string'],
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
            'type.required' => 'La forma de pago es requerida.',
            'type.string' => 'La forma de pago debe ser una cadena.',
            'type.in' => 'La forma de pago no está dentro de la lista de opciones.',
            'type.min' => 'La forma de pago tiene que tener mínimo 1 caracter.',
            'type.max' => 'La forma de pago tiene que tener máximo 2 caracteres.',
            'description.required' => 'La descripción es requerida.',
            'description.string' => 'La descripción debe ser una cadena.',
            'description.min' => 'La descripción tiene que tener mínimo 10 caracteres.',
            'description.max' => 'La descripción tiene que tener máximo 255 caracteres.',
            'tones.required' => 'Los timbres son requeridos.',
            'tones.integer' => 'Los timbres deben ser un número entero.',
            'gitf_tones.required' => 'Los timbres son requeridos.',
            'gitf_tones.integer' => 'Los timbres deben ser un número entero.',
            'amount.required' => 'La cantidad es requerida.',
            'amount.numeric' => 'La cantidad debe ser un número.',
            'date.required' => 'La fecha es requerida.',
            'date.string' => 'La fecha debe ser una cadena.',
            'company_id.required' => 'La empresa es requerida.',
            'company_id.string' => 'La empresa debe ser una cadena.',
            'company_id.exists' => 'La empresa no existe.',
        ];
    }
}
