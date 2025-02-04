<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Insurance;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InsuranceController extends Controller
{
    public function index()
    {
        $seguro = Insurance::all();
        return ApiResponse::success(
            'Lista de seguros',
            200,
            $seguro
        );
    }

    public function store(InsuranceRequest $request)
    {
        try {
            $seguro = Insurance::create($request->all());
            return ApiResponse::success('Seguro creado exotosamente', 200, $seguro);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Errores de validacion:', 422, $errors);
        }
    }

    public function show($id)
    {
        try {
            $seguro = Insurance::findOrFail($id);
            return ApiResponse::success('Seguro obtenido exitosamente', 200, $seguro);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('seguro no encontrado', 404);
        }
    }

    public function update(InsuranceRequest $request, string $id)
    {
        try {
            $seguro = Insurance::findOrFail($id);
            $seguro->update($request->all());
            return ApiResponse::success('Seguro actualizado exitosamente', 200, $seguro);
        } catch (Exception $e) {
            return ApiResponse::error('Error al actualizar el auto', $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }

    public function destroy(Insurance $insurance)
    {
        //
    }
}
