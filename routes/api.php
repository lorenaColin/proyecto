<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Controladores_Sat\CpController;
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
Route::get('verificarCP', [CpController::class, 'verificarCP']);
 // token 
Route::post('refresh', [UserController::class, 'refreshToken']);
