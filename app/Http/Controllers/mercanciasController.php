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
        return ApiResponse::success('Mercancia creado correctamente', 201, $mercancia);
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
            return ApiResponse::error('Mercancia no encontrada', 404);
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
    
            return ApiResponse::success('Mercancia actualizada correctamente', 200, $mercancia);
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
        return ApiResponse::success('Mercancia eliminado correctamente', 200);
    }
    // public function prodservcp()
    // {
    //     $productos = Cat_prodServCP::all();
    //     return ApiResponse::success('Listado de mercancia', 200, $productos); 
    // }
    public function prodservcp(Request $request)
    {
        $termino = $request->input('termino', '');
        $unidades = Cat_prodServCP::where('c_ClaveProdServ', 'like', "%$termino%")
                               ->orWhere('descripcion', 'like', "%$termino%")
                               ->orWhere('descripcion2', 'like', "%$termino%")
                               ->orWhere('material_peligroso', 'like', "%$termino%")

                               ->get();
    
        return ApiResponse::success('Listado de mercancia', 200, $unidades);
    }
       
   
    public function catClaveUnidad(Request $request)
    {
        $termino = $request->input('termino', '');
        $unidades = Cat_claveUnidad::where('c_claveunidad', 'like', "%$termino%")
                               ->orWhere('nombre', 'like', "%$termino%")
                              

                               ->get();
    
        return ApiResponse::success('Listado de clave', 200, $unidades);
    }
       
 
    public function catMatpeligroso(Request $request)
    {
        $termino = $request->input('termino', '');
        $unidades = cat_materialpeligroso::where('clave', 'like', "%$termino%")
                               ->orWhere('descripcion', 'like', "%$termino%")
                              

                               ->get();
    
        return ApiResponse::success('Listado de material Peligroso', 200, $unidades);
    }
    public function catEmbalaje()
    {
        $embalaje = cat_embalaje::all();
        return ApiResponse::success('Listado de Embalajes ', 200, $embalaje); 
    }
    
    
}
