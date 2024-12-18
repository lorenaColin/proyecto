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
}
