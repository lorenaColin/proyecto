<?php


namespace App\Http\Requests;


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
        if ($this->isMethod('post') && $this->path() === 'api/resendcode') {
            return [
                'email' => ['required', 'email', 'exists:users,email'],
            ];
        }
        if ($this->isMethod('post') && $this->path() === 'api/verifycode') {
            return [
                'code' => ['required', 'string', 'size:4'],
            ];
        }

        return [
            'name' => ['required', 'string', 'min:10', 'max:75'],
            'email' => [  'required', 'string','email', Rule::unique('users', 'email')->ignore($this->route('user')) ],
            'password' => ['required', 'string', 'min:8', 'max:12'],
            'type' => ['required', 'string', Rule::in(['user', 'mrfc'])],
           
    
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 10 caracteres.',
            'name.max' => 'El nombre debe tener como máximo 75 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'Este email ya está en uso.',
            'code.required' => 'El código de verificación es obligatorio.',
            'code.string' => 'El código de verificación debe ser una cadena.',
            'code.size' => 'El código de verificación debe tener exactamente 4 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña debe tener como máximo 12 caracteres.',
            'type.required' => 'El tipo de usuario es obligatorio.',
            'type.string' => 'El tipo de usuario debe ser una cadena de texto.',
            'type.in' => 'El tipo de usuario debe ser "user" o "multirfc".',
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
}
