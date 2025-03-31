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
        return ApiResponse::success('Listado de Ubicaciones', 200, $ubicacion); 
    }

    public function getUbicacionesPorTipo($tipoUbicacion)
    {
        $ubicaciones = Ubication::where('tipoUbicacion', $tipoUbicacion)->get();
        if ($ubicaciones->isEmpty()) {
            return ApiResponse::success('No se encontraron ubicaciones para el tipo: ' . $tipoUbicacion, 200, []);
        }
        return ApiResponse::success('Listado de Ubicaciones de tipo: ' . $tipoUbicacion, 200, $ubicaciones);
    }
  

   
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(ubicationRequest $request)
    {
        $ubicacion = ubication::create($request->validated());
        return ApiResponse::success('UbicacioN creada exitosamente', 201, $ubicacion);
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
    
            return ApiResponse::success('Ubicacion actualizada correctamente', 200, $ubicacion);
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
        return ApiResponse::success('Ubicaciones eliminada exitosamente', 200);
    }
    // public function catPais()
    // {
    //     $pais = Cat_pais::all();
    //     return ApiResponse::success('Listado de unidades ', 200, $pais); 
    // }
    public function catPais(Request $request)
{
    $termino = $request->input('termino', '');
    $unidades = Cat_pais::where('c_pais', 'like', "%$termino%")
                           ->orWhere('descripcion', 'like', "%$termino%")
                           ->get();

    return ApiResponse::success('Listado de unidades', 200, $unidades);
}
    

   
    public function buscarDireccion($codigoPostal)
    {
        if (empty($codigoPostal)) {
            return response()->json(['error' => 'Código postal no proporcionado'], 400);
        }
    
        $direccion = DB::table('catalogos.cat_cp as cc')
            ->select(
                'cc.cat_codigo',
                'cc.cat_municipio',
                'ce.c_estado',
                'ce.nombre_estado as Nombreestado', 
                'cm.descripcion as municipio', 
                DB::raw('(SELECT IFNULL(JSON_ARRAYAGG(CONCAT(cc2.c_colonia, "|", cc2.asentamiento)), JSON_ARRAY()) 
                         FROM catalogos.cat_colonia cc2 WHERE cc2.c_cp = cc.cat_codigo) as colonias'),
                DB::raw('(SELECT IFNULL(JSON_ARRAYAGG(CONCAT(cl.c_localidad, "|", cl.descripcion)), JSON_ARRAY()) 
                         FROM catalogos.cat_localidad cl WHERE cl.c_localidad = cc.cat_localidad AND cl.c_estado = cc.cat_estado) as localidades')
            )
            ->join('catalogos.cat_estado as ce', 'cc.cat_estado', '=', 'ce.c_estado')
            ->join('catalogos.cat_municipio as cm', function ($join) {
                $join->on('cm.c_municipio', '=', 'cc.cat_municipio')
                    ->on('cm.c_estado', '=', 'cc.cat_estado');
            })
            ->where('cc.cat_codigo', $codigoPostal)
            ->first();
    
        if (!$direccion) {
            return response()->json(['error' => 'No se encontró la dirección'], 404);
        }
    
        $direccion->colonias = json_decode($direccion->colonias, true) ?: [];
        $direccion->localidades = json_decode($direccion->localidades, true) ?: [];
    
        return response()->json([
            'cat_codigo' => $direccion->cat_codigo,
            'cat_municipio' => $direccion->cat_municipio,
            'c_estado' => $direccion->c_estado,
            'Nombreestado' => $direccion->Nombreestado, 
            'municipio' => $direccion->municipio, 
            'colonias' => $direccion->colonias,
            'localidades' => $direccion->localidades
        ]);
    }
    

}

