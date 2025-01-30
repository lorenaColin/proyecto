<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutotransportRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Autotransport;
use App\Models\Models\CatalogoSat\cat_configVehiculo;
use App\Models\Models\CatalogoSat\cat_tipoPermiso;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AutotransportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auto = Autotransport::all();

        return ApiResponse::success(
            'Lista de autotransportes',
            200,
            $auto
        );
    }
    public function store(AutotransportRequest $request)
    {
        try {
            $auto = Autotransport::create($request->all());
            return ApiResponse::success('Autotransporte creado correctamente', 201, $auto);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Errores de validacion:', 422, $errors);
        }
    }
    public function show($id)
    {
        try {
            $auto = Autotransport::findOrFail($id);
            return ApiResponse::success('Autotransporte obtenido exitosamente', 200, $auto);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('auto no encontrado', 404);
        }
    }
    public function update(AutotransportRequest $request,  string $id)
    {
        try {
            $auto = Autotransport::findOrFail($id);
            $auto->update($request->all());

            return ApiResponse::success('Autotransporte actualizado correctamente', 200, $auto);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el auto', $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }

    public function destroy(Autotransport $autotransport)
    {
        //
    }

    public function getConfigAutotransporte()
    {
        try {
            $configuraciones = cat_configVehiculo::select('nomenclature', 'description', 'remolq')->get();
            return ApiResponse::success('Lista de configuraciones', 200, $configuraciones);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener datos', 500, $e->getMessage());
        }
    }

    public function getPermisos()
    {
        try {

            $configuraciones = cat_tipoPermiso::select('permission', 'description', 'transport')
            ->where('transport', 'LIKE', '%01%')
            ->get();

            return ApiResponse::success('Lista de configuraciones', 200, $configuraciones);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener datos', 500, $e->getMessage());
        }
    }
}
