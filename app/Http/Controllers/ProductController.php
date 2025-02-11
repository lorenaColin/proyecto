<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\SerieRequest;
use App\Models\Models\CatalogoSat\Cat_claveUnidad;
use App\Models\Models\CatalogoSat\ClaveProdServ;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }
    

    /**
     * Show the form for creating a new resource.
     */
   

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        // Los datos ya están validados en ProductRequest
        $product = Product::create($request->validated());
        return ApiResponse::success('Producto creado correctamente', 201, $product);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return ApiResponse::success('product obtenida exitosamente', 200, $product);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('product no encontrada', 404);
        }
    }

 

    /**
     * Show the form for editing the specified resource.
     */
  
    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $producto = Product::find(id: $id);  
    
            if (!$producto) {
                return ApiResponse::error('producto no encontrada', 404, 'La producto con el ID proporcionado no existe');
            }
    
            $producto->update($request->validated());
    
            return ApiResponse::success('producto actualizada correctamente', 200, $producto);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la producto', 
            $e instanceof ValidationException ? 422 : 500, $e->getMessage());
        }
    }
    
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::error('Producto no encontrado', 404);
        }
        $product->delete();
        return ApiResponse::success('Producto eliminado correctamente', 200);
    }
    public function catProductos()
    {
        $producto = ClaveProdServ::all();
        return ApiResponse::success('Listado de productos ', 200, $producto); 
    }
    public function catUnidad()
    {
        $unidad = Cat_claveUnidad::all();
        return ApiResponse::success('Listado de unidades ', 200, $unidad); 
    }
}
