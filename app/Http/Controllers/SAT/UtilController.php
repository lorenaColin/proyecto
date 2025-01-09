<?php

namespace App\Http\Controllers\SAT;

use App\Http\Controllers\Controller;
use App\Http\Requests\SatRequest;
use App\Models\Models\CatalogoSat\catPostCode;
use Exception;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Models\CatalogoSat\Cat_claveUnidad;
use App\Models\Models\CatalogoSat\Cat_pais;
use App\Models\Models\CatalogoSat\ClaveProdServ;

class UtilController extends Controller
{
    public function searchCodePostal(SatRequest $request): JsonResponse
    {
        try {
            $cp = $request->cp;
            $existe = catPostCode::where('cat_codigo', $cp)->exists();
            return !$existe ? ApiResponse::error('Código postal no encontrado', 401) : ApiResponse::success('Código postal encontrado',  200);
        } catch (Exception $e) {
            return ApiResponse::error('Código postal no encontrado', 500, $e->getMessage());
        }
    }

    public function searchClavProdSer(Request $request): JsonResponse
    {
       
            $clave = $request->termino; 
    
            if (!$clave) {
                return ApiResponse::error('Debe proporcionar un término para la búsqueda', 400);
            }

            $customers = ClaveProdServ::select('c_claveprodser', 'descripcion')
                ->where('c_claveprodser', 'LIKE', '%' . $clave . '%')
                ->get();
            if ($customers->isEmpty()) {
                return ApiResponse::error('No se encontraron resultados', 404);
            }
            return ApiResponse::success('Resultados de la búsqueda', 200, $customers); 
    }
    
    public function searchClavUnidad(Request $request): JsonResponse
    {
       
            $clave = $request->termino; 
    
            if (!$clave) {
                return ApiResponse::error('Debe proporcionar un término para la búsqueda', 400);
            }

            $customers = Cat_claveUnidad::select('c_claveunidad', 'nombre')
                ->where('c_claveunidad', 'LIKE', '%' . $clave . '%')
                ->get();
            if ($customers->isEmpty()) {
                return ApiResponse::error('No se encontraron resultados', 404);
            }
            return ApiResponse::success('Resultados de la búsqueda', 200, $customers); 
    }
    public function searchPais(Request $request): JsonResponse
    {
       
            $clave = $request->termino; 
    
            if (!$clave) {
                return ApiResponse::error('Debe proporcionar un término para la búsqueda', 400);
            }
            $customers = Cat_pais::select('descripcion', 'c_pais')
                ->where('descripcion', 'LIKE', '%' . $clave . '%')
                ->get();
            if ($customers->isEmpty()) {
                return ApiResponse::error('No se encontraron resultados', 404);
            }
            return ApiResponse::success('Resultados de la búsqueda', 200, $customers); 
    }


    

}
