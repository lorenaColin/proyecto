<?php

namespace App\Http\Controllers;

use App\Http\Requests\remolqueRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Models\CatalogoSat\cat_cp_remolque;
use App\Models\remolques;
use Illuminate\Http\Request;

class remolquesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $remolques = remolques::all();
        return ApiResponse::success('Listado de remolques', 200, $remolques); 
    }
    /**
     * Show the form for creating a new resource.
     */
  
    /**
     * Store a newly created resource in storage.
     */
    // public function store(remolqueRequest $request)
    // {
    //     $remolques = remolques::create($request->validated());
    //     return ApiResponse::success('remolques creada exitosamente', 201, $remolques);
    // }
    public function store(remolqueRequest $request)
    {
        \Log::info('SubTipoRem: ' . $request->SubTipoRem);
    
        $remolque = cat_cp_remolque::where('Clave', $request->SubTipoRem)->first();
    
        if (!$remolque) {
            return ApiResponse::error('Descripción de remolque no encontrada', 404);
        }
    
        $nuevoRemolque = remolques::create([
            'SubTipoRem' => $remolque->Clave, 
            'placa' => $request->placa,  
            'uuid_company' => $request->uuid_company,  
        ]);
    
       
        return ApiResponse::success('Remolque creado exitosamente', 201, $nuevoRemolque);
    }
    

    
    


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $remolques = remolques::find($id);
    
        if (!$remolques) {
            return ApiResponse::error('remolques no encontrada', 404, 'La remolques con el ID proporcionado no existe');
        }
    
        return ApiResponse::success('Detalle de la remolques', 200, $remolques);
    }

    /**
     * Show the form for editing the specified resource.
     */
   
    /**
     * Update the specified resource in storage.
     */
    public function update(remolqueRequest $request, $id)
    {
        try {
            $remolque = remolques::find(id: $id);  
    
            if (!$remolque) {
                return ApiResponse::error('remolque no encontrada', 404, 'El remolque con el ID proporcionado no existe');
            }
    
            $remolque->update($request->validated());
    
            return ApiResponse::success('remolque actualizado correctamente', 200, $remolque);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el remolque', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(remolques $remolques)
    {
        $remolques->delete();
        return ApiResponse::success('remolque eliminado exitosamente', 200);
    }

    // public function catRemolques()
    // {
    //     $descripciones = cat_cp_remolque::pluck('descripcion'); 
    //     return response()->json($descripciones);
    // }
    public function catRemolques()
{
    $remolques = cat_cp_remolque::select('Clave', 'descripcion')->get(); 
    return response()->json($remolques);
}
}
