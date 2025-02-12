<?php

namespace App\Http\Controllers;

use App\Http\Requests\mercanciasRequest;
use App\Http\Responses\ApiResponse;
use App\Models\mercancia;
use App\Models\Models\CatalogoSat\Cat_claveUnidad;
use App\Models\Models\CatalogoSat\cat_embalaje;
use App\Models\Models\CatalogoSat\cat_materialpeligroso;
use App\Models\Models\CatalogoSat\Cat_prodServCP;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class mercanciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mercancia = mercancia::all();
        return ApiResponse::success('Listado de remolques', 200, $mercancia); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(mercanciasRequest $request)
    {
        // Los datos ya están validados en mercanciaRequest
        $mercancia= mercancia::create($request->validated());
        return ApiResponse::success('mercancia creado correctamente', 201, $mercancia);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $mercancia = mercancia::findOrFail($id);
            return ApiResponse::success('mercancia obtenida exitosamente', 200, $mercancia);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('mercancia no encontrada', 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(mercanciasRequest $request, $id)
    {
        try {
            $mercancia = mercancia::find(id: $id);  
    
            if (!$mercancia) {
                return ApiResponse::error('mercancia no encontrada', 404, 'La mercancia con el ID proporcionado no existe');
            }
    
            $mercancia->update($request->validated());
    
            return ApiResponse::success('mercancia actualizada correctamente', 200, $mercancia);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la mercancia', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mercancia = mercancia::find($id);
        if (!$mercancia) {
            return ApiResponse::error('mercancia no encontrado', 404);
        }
        $mercancia->delete();
        return ApiResponse::success('mercancia eliminado correctamente', 200);
    }
    public function prodservcp()
    {
        $productos = Cat_prodServCP::all();
        return ApiResponse::success('Listado de mercancia', 200, $productos); 
    }
    public function catClaveUnidad()
    {
        $unidad = Cat_claveUnidad::all();
        return ApiResponse::success('Listado de clave ', 200, $unidad); 
    }
    public function catMatpeligroso()
    {
        $materialP = cat_materialpeligroso::all();
        return ApiResponse::success('Listado de clave ', 200, $materialP); 
    }
    public function catEmbalaje()
    {
        $embalaje = cat_embalaje::all();
        return ApiResponse::success('Listado de Embalajes ', 200, $embalaje); 
    }
    
    
}
