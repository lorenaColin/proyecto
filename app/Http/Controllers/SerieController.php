<?php

namespace App\Http\Controllers;

use App\Http\Requests\SerieRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SerieController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
            $Series = Serie::all();
            return ApiResponse::success('Listado de usuarios', 200, $Series);   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SerieRequest $request)
    {
        $serie = Serie::create($request->validated());
        return ApiResponse::success('Serie creada exitosamente', 201, $serie);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $serie = Serie::find($id);

    if (!$serie) {
        return ApiResponse::error('Serie no encontrada', 404, 'La serie con el ID proporcionado no existe');
    }

    return ApiResponse::success('Detalle de la serie', 200, $serie);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(SerieRequest $request, $id)
    {
        try {
            $serie = Serie::find(id: $id);  
    
            if (!$serie) {
                return ApiResponse::error('Serie no encontrada', 404, 'La serie con el ID proporcionado no existe');
            }
    
            $serie->update($request->validated());
    
            return ApiResponse::success('Serie actualizada correctamente', 200, $serie);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la serie', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }
    
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Serie $serie)
    {
        $serie->delete();
        return ApiResponse::success('Serie eliminada exitosamente', 200);
    }
    
}
