<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        'product_key' => ['required', 'string', 'max:8'],
        
        'unit' => ['required', 'string', 'max:5'],
        'unit_description' => ['nullable', 'string', 'max:1000'],
        'unit_price' => ['required', 'numeric', 'min:0'],
        'quantity' => ['required', 'numeric', 'min:0'],
        'status' => ['required', 'boolean'],
        'identifier_number' => ['required', 'string','max:100'],
    //    'internal_key' => ['required', 'string',],
    //    Rule::unique('products', 'internal_key')->ignore($this->route('product')),
        'internal_key' => [
                'required',
                'string',
                Rule::unique('products', 'internal_key')
                    ->where('uuid_company', $this->input('uuid_company'))
                    ->ignore($this->route('product')), 
            ],
        'description' => ['nullable', 'string'],
        'uuid_company' => ['required', 'uuid', 'exists:companies,id'],
    ];
}

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

public function messages()
{
    return [
        'product_key.required' => 'La clave del producto es requerida.',
        'product_key.string' => 'La clave del producto debe ser una cadena.',
        'product_key.max' => 'La clave del producto no puede tener más de 8 caracteres.',
        
        'unit.required' => 'La unidad es requerida.',
        'unit.string' => 'La unidad debe ser una cadena.',
        'unit.max' => 'La unidad no puede tener más de 5 caracteres.',
        
        'unit_description.string' => 'La descripción de la unidad debe ser una cadena.',
        'unit_description.max' => 'La descripción de la unidad no puede tener más de 1000 caracteres.',
        
        'unit_price.required' => 'El precio unitario es requerido.',
        'unit_price.numeric' => 'El precio unitario debe ser un valor numérico.',
        'unit_price.min' => 'El precio unitario debe ser mayor o igual a 0.',
        
        'quantity.required' => 'La cantidad es requerida.',
        'quantity.numeric' => 'La cantidad debe ser un valor numérico.',        
        'status.required' => 'El estado debe ser requerdio',

        
        'identifier_number.required' => 'El número de identificación es requerido.',
        'identifier_number.string' => 'El numero  de identificación debe ser un string.',
        
        'internal_key.required' => 'La clave interna es requerida.',
        'internal_key.string' => 'La clave interna debe ser una cadena.',
        'internal_key.unique' => 'La clave interna debe ser única.',
        
        'description.string' => 'La descripción debe ser una cadena.',
        
        'uuid_company.required' => 'La empresa es requerida.',
        'uuid_company.uuid' => 'El identificador de la empresa debe ser un UUID válido.',
        'uuid_company.exists' => 'La empresa especificada no existe.',
    ];
}

    
}
