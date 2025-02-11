<?php

use App\Http\Controllers\mercanciasController;
use App\Http\Controllers\remolquesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyDetailController;
use App\Http\Controllers\SAT\UtilController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SerieController;

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ubicationController;

// inicio de sesion
Route::post('login', [UserController::class, 'login']);  
Route::apiResource('users', UserController::class);
Route::post('verifycode', [UserController::class, 'verifyCode']);
Route::get('resendcode', [UserController::class, 'resendcode']);


// Route::middleware('auth:api')->group(function () {
//company
Route::apiResource('companies', CompanyController::class);
Route::post('companies/loadSeals', [CompanyDetailController::class, 'loadSeals']);
// clientes
Route::apiResource('customers', CustomerController::class);
Route::post('customerbyuser', [CustomerController::class, 'customerbyuser']);

//CatalogosSat 
Route::get(uri: 'searchCodePostal', action: [UtilController::class, 'searchCodePostal']);
Route::get(uri: 'searchClavProdSer', action: [UtilController::class, 'searchClavProdSer']);
Route::get(uri: 'searchClavUnidad', action: [UtilController::class, 'searchClavUnidad']);
Route::get(uri: 'searchPais', action: [UtilController::class, 'searchPais']);




Route::get('searchCodePostal', [UtilController::class, 'searchCodePostal']);
// });


 // token 
Route::post('refresh', [UserController::class, 'refresh']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:api');

//Series 
Route::apiResource('serie', SerieController::class);
// productos
Route::apiResource('products', ProductController::class);
Route::get('catProductos', [ProductController::class, 'catProductos']);
Route::get('catUnidad', [ProductController::class, 'catUnidad']);

// invoice
Route::apiResource('invoices', InvoiceController::class);
//carta porte ubicaciones
Route::apiResource('ubicacion', ubicationController::class);
Route::get(uri: 'paises', action: [ubicationController::class, 'catPais']);
Route::get('direccion/{codigoPostal}', [ubicationController::class, 'buscarDireccion']);
//remolques
Route::apiResource('remolques', remolquesController::class);
Route::get(uri: 'catRemolques', action: [remolquesController::class, 'catRemolques']);
//mercancias 
Route::apiResource('mercancia', mercanciasController::class);
Route::get('prodservcp', [mercanciasController::class, 'prodservcp']);
Route::get('catClaveUnidad', [mercanciasController::class, 'catClaveUnidad']);
Route::get('catMatpeligroso', [mercanciasController::class, 'catMatpeligroso']);
Route::get('catEmbalaje', [mercanciasController::class, 'catEmbalaje']);









