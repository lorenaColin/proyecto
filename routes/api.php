<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::apiResource('users', UserController::class);
// Route::apiResource('companies', CompanyController::class);
// Route::post('logIn', [UserController::class, 'login']);
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Inicio de sesion
Route::post('InicioSesion', [UserController::class, 'inicioSesion']);  
Route::apiResource('users', UserController::class);
Route::post('verifycode', [UserController::class, 'verifyCode']);
Route::get('resendcode', [UserController::class, 'resendcode']);

//company
Route::apiResource('companies', CompanyController::class);
Route::apiResource('customers', CustomerController::class);
Route::post('customerbyuser', [CustomerController::class, 'customerbyuser']);