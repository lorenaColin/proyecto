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
use Illuminate\Support\Facades\DB;

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
    // public function catPais()
    // {
    //     $descripciones = Cat_pais::pluck('descripcion'); 
    //     return response()->json($descripciones);
    // }

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
    // public function buscarDireccion($codigoPostal)
    // {
    //     if (empty($codigoPostal)) {
    //         return response()->json(['error' => 'Código postal no proporcionado'], 400);
    //     }
    //     $codigoPostalInfo = Cat_cp::where('cat_codigo', $codigoPostal)->first();
        
    //     if (!$codigoPostalInfo) {
    //         return response()->json(['error' => 'Código postal no encontrado'], 404);
    //     }
    //     $estado = cat_estado::where('c_estado', $codigoPostalInfo->cat_estado)->first();

    //     $municipio = cat_municipio::where('c_municipio', $codigoPostalInfo->cat_municipio)
    //         ->where('c_estado', $codigoPostalInfo->cat_estado)
    //         ->first();
    //     $localidades = cat_localidad::where('c_estado', operator: $codigoPostalInfo->cat_estado)->get();
    //     $colonias = cat_colonia::where('c_cp', $codigoPostal)->get();
    //     $datosDireccion = [
    //         'estado' => $estado ? $estado->nombre_estado : null,
    //         'municipio' => $municipio ? $municipio->descripcion : null,
    //         'localidades' => $localidades->map(function ($localidad) {
    //             return $localidad->descripcion;
    //         }),
    //         'colonias' => $colonias->map(function ($colonia) {
    //             return $colonia->asentamiento;
    //         }),
    //     ];
    //     return response()->json($datosDireccion);
    // }
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
    
 public function figurasquery(Request $request)
{
    $termino = $request->input('termino', '');

    $conceptos = figuras::where('rfcFigura', 'like', "%$termino%")
                           ->orWhere('numLicencia', 'like', "%$termino%")
                           ->orWhere('nombreFigura', 'like', "%$termino%")


                           ->get();

    return ApiResponse::success('Listado de figuras', 200, $conceptos);
}
}
