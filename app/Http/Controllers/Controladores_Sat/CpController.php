<?php

namespace App\Http\Controllers\Controladores_Sat;

use App\Http\Controllers\Controller;
use App\Http\Requests\SatRequest;
use App\Models\Models\CatalogoSat\catPostCode;
use Exception;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CpController extends Controller
{
    public function verificarCP(SatRequest $request): JsonResponse
    {
        try {
          
            $cp = $request->cp;
            $existe = catPostCode::where('cat_codigo', $cp)->exists();
            return !$existe ? response()->json([
                'error' => true,
                'msg' => 'Código postal no encontrado',
            ], 404) : response()->json([
                'error' => false,
                'msg' => 'Código postal encontrado',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }
}
