<?php

namespace App\Http\Controllers;

use App\Http\Requests\ubicationRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Models\CatalogoSat\cat_colonia;
use App\Models\Models\CatalogoSat\Cat_cp;
use App\Models\Models\CatalogoSat\cat_estado;
use App\Models\Models\CatalogoSat\cat_localidad;
use App\Models\Models\CatalogoSat\cat_municipio;
use App\Models\Models\CatalogoSat\Cat_pais;
use App\Models\ubication;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ubicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ubicacion = ubication::all();
        return ApiResponse::success('Listado de ubicaciones', 200, $ubicacion); 
    }

    public function getUbicacionesPorTipo($tipoUbicacion)
    {
        $ubicaciones = Ubication::where('tipoUbicacion', $tipoUbicacion)->get();
        if ($ubicaciones->isEmpty()) {
            return ApiResponse::success('No se encontraron ubicaciones para el tipo: ' . $tipoUbicacion, 200, []);
        }
        return ApiResponse::success('Listado de ubicaciones de tipo: ' . $tipoUbicacion, 200, $ubicaciones);
    }
  

   
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(ubicationRequest $request)
    {
        $ubicacion = ubication::create($request->validated());
        return ApiResponse::success('ubicacion creada exitosamente', 201, $ubicacion);
    }

    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ubicacion = ubication::find($id);
    
        if (!$ubicacion) {
            return ApiResponse::error('ubicacion no encontrada', 404, 'La ubicacion con el ID proporcionado no existe');
        }
    
        return ApiResponse::success('Detalle de la ubicacion', 200, $ubicacion);
    }

  
    /**
     * Update the specified resource in storage.
     */
    public function update(ubicationRequest $request, $id)
    {
        try {
            $ubicacion = ubication::find(id: $id);  
    
            if (!$ubicacion) {
                return ApiResponse::error('ubicacion no encontrada', 404, 'La ubicacion con el ID proporcionado no existe');
            }
    
            $ubicacion->update($request->validated());
    
            return ApiResponse::success('ubicacion actualizada correctamente', 200, $ubicacion);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la ubicacion', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ubication $ubicacion)
    {
        $ubicacion->delete();
        return ApiResponse::success('ubicacion eliminada exitosamente', 200);
    }
    public function catPais()
    {
        $pais = Cat_pais::all();
        return ApiResponse::success('Listado de unidades ', 200, $pais); 
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

