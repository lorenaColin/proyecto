<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::apiResource('users', UserController::class);
// Route::apiResource('companies', CompanyController::class);
// Route::post('logIn', [UserController::class, 'login']);
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('logIn', [UserController::class, 'loginInicio']);  
Route::post('Autenticacion', [UserController::class, 'auth']);  
Route::apiResource('users', UserController::class);    

Route::middleware('auth:api')->group(function () {
Route::apiResource('companies', CompanyController::class);

    
});
