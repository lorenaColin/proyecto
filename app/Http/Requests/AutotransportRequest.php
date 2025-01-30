<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AutotransportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'configVehicular' => 'required|string|max:11',
            'pesoBrutoVehicular' => 'required|decimal:2',
            'placaVM' => [
                'required',
                'string',
                'min:5',
                'max:7',
                Rule::unique('autotransports', 'placaVM')
                ->where('company_id', $this->input('company_id'))
                ->ignore($this->route('autotransport')), 
            ],
            'anioModeloVM' => 'required|integer',
            'permSCT' => 'required|string|max:6',
            'numPermisoSCT' => 'required|string|max:50',
            'aseguraRespCivil' => 'required|string|max:50',
            'polizaRespCivil' => 'required|string|max:30',
            'company_id' => 'required|string|max:36',
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
            'configVehicular.required' => 'La Clave Vehicular es obligatoria',
            'pesoBrutoVehicular.required' => 'El Peso Bruto es obligatorio',
            'placaVM.required' => 'La Placa es obligatoria',
            'anioModeloVM.required' => 'El Año del Modelo es obligatorio',
            'permSCT.required' => 'El Permiso SCT es obligatorio',
            'numPermisoSCT.required' => 'El Número del Permiso es obligatorio',
            'aseguraRespCivil.required' => 'La Aseguradora Civil es obligatoria',
            'polizaRespCivil.required' => 'El número de Poliza es obligatorio',
            'company_id.required' => 'La compañia es obligatoria',
        ];
    }
}
