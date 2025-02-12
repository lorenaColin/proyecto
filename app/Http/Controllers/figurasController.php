<?php

namespace App\Http\Controllers;

use App\Http\Requests\figurasRequest;
use App\Http\Responses\ApiResponse;
use App\Models\figuras;
use App\Models\Models\CatalogoSat\cat_colonia;
use App\Models\Models\CatalogoSat\Cat_cp;
use App\Models\Models\CatalogoSat\cat_estado;
use App\Models\Models\CatalogoSat\cat_localidad;
use App\Models\Models\CatalogoSat\cat_municipio;
use App\Models\Models\CatalogoSat\Cat_pais;
use Illuminate\Http\Request;

class figurasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $figuras = figuras::all();
        return ApiResponse::success('Listado de figuras', 200, $figuras); 
    }
  public function store(figurasRequest $request)
    {
        $figuras = figuras::create($request->validated());
        return ApiResponse::success('figuras creada exitosamente', 201, $figuras);
    }

 public function show($id)
    {
        $figuras = figuras::find($id);
    
        if (!$figuras) {
            return ApiResponse::error('figuras no encontrada', 404, 'La figuras con el ID proporcionado no existe');
        }
    
        return ApiResponse::success('Detalle de la figuras', 200, $figuras);
    }
 public function update(figurasRequest $request, $id)
    {
        try {
            $figuras = figuras::find(id: $id);  
    
            if (!$figuras) {
                return ApiResponse::error('figuras no encontrada', 404, 'La figuras con el ID proporcionado no existe');
            }
    
            $figuras->update($request->validated());
    
            return ApiResponse::success('figuras actualizada correctamente', 200, $figuras);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la figuras', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }
 public function destroy(figuras $figuras)
    {
        $figuras->delete();
        return ApiResponse::success('figuras eliminada exitosamente', 200);
    }
    public function catPais()
    {
        $descripciones = Cat_pais::pluck('descripcion'); 
        return response()->json($descripciones);
    }
    public function buscarDireccion($codigoPostal)
    {
        if (empty($codigoPostal)) {
            return response()->json(['error' => 'Código postal no proporcionado'], 400);
        }
        $codigoPostalInfo = Cat_cp::where('cat_codigo', $codigoPostal)->first();
        
        if (!$codigoPostalInfo) {
            return response()->json(['error' => 'Código postal no encontrado'], 404);
        }
        $estado = cat_estado::where('c_estado', $codigoPostalInfo->cat_estado)->first();

        $municipio = cat_municipio::where('c_municipio', $codigoPostalInfo->cat_municipio)
            ->where('c_estado', $codigoPostalInfo->cat_estado)
            ->first();
        $localidades = cat_localidad::where('c_estado', operator: $codigoPostalInfo->cat_estado)->get();
        $colonias = cat_colonia::where('c_cp', $codigoPostal)->get();
        $datosDireccion = [
            'estado' => $estado ? $estado->nombre_estado : null,
            'municipio' => $municipio ? $municipio->descripcion : null,
            'localidades' => $localidades->map(function ($localidad) {
                return $localidad->descripcion;
            }),
            'colonias' => $colonias->map(function ($colonia) {
                return $colonia->asentamiento;
            }),
        ];
        return response()->json($datosDireccion);
    }
}
