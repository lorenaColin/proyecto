<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SAT\UtilController;
use App\Http\Controllers\CustomerController;
// inicio de sesion
Route::post('login', [UserController::class, 'login']);  
Route::apiResource('users', UserController::class);
Route::post('verifycode', [UserController::class, 'verifyCode']);
Route::get('resendcode', [UserController::class, 'resendcode']);

//company
Route::apiResource('companies', CompanyController::class);
// clientes
Route::apiResource('customers', CustomerController::class);
Route::post('customerbyuser', [CustomerController::class, 'customerbyuser']);

//CatalogosSat 
Route::get(uri: 'searchCodePostal', action: [UtilController::class, 'searchCodePostal']);
Route::get(uri: 'searchClavProdSer', action: [UtilController::class, 'searchClavProdSer']);
Route::get(uri: 'searchClavUnidad', action: [UtilController::class, 'searchClavUnidad']);
Route::get(uri: 'searchPais', action: [UtilController::class, 'searchPais']);






 // token 
Route::post('refresh', [UserController::class, 'refresh']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:api');
