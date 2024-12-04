<?php


namespace App\Http\Request;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'email' =>  ['required', 'string', 'email', Rule::unique('users')->ignore($this->route('user'))],
            'password' => ['required', 'string', 'min:8', 'max:12'],
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
            'email.required' => 'El correo  es requerido.',
            'email.string' => 'El correo debe ser una cadena.',
            'email.email' => 'El correo no tiene un formato valiido.',
            'email.unique' => 'El correo ya es encuentra en uso',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña debe tener como máximo 12 caracteres.',
            
           
        ];
    }
}
