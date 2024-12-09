<?php

namespace App\Http\Controllers;


//Modelos
use App\Models\Customer;


//Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

//Terceros
use App\Http\Requests\CustomerRequest;
use App\Http\Responses\ApiResponse;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        try {           
            $cliente = Customer::create($request->validated());
            return ApiResponse::success('Cliente creado correctamente', 201, $cliente);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Errores de validación: ', 422, $errors);
        } catch (\Throwable $e) {
            return ApiResponse::error('Error al crear el cliente', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cliente = Customer::findOrFail($id);
            return ApiResponse::success('Cliente obtenido exitosamente', 200, $cliente);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Cliente no encontrado', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, string $id)
    {
        try {
            
            $cliente = Customer::findOrFail($id);
            $cliente->update($request->validated());

            return ApiResponse::success('Cliente actualizado correctamente', 200, $cliente);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el cliente', $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
