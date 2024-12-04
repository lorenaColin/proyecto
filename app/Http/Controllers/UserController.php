<?php

namespace App\Http\Controllers;

use App\Http\Request\UserRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { {
            $users = User::all();
            return ApiResponse::success('Listado de usuarios', 200, $users);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $existie = User::where('email', $request->email)->first();
        if ($existie) {
            return ApiResponse::error('El correo electrónico ya está registrado.', 400);
        }   
        $user = new User();
        $user->email = $request->email;  
        $user->password = Hash::make($request->password); 
        $user->multi_rfc = 0; 
        $user->status = 'Activo'; 
        $user->ultima_conexion = now(); 
        $user->save();
        return ApiResponse::success('Usuario registrado con éxito', 201, [
            'user' => $user
        ]);
    }
    public function login(UserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

       if ($user && Hash::check($request->password, $user->password)) {
            return ApiResponse::success('Inicio de sesión exitoso', 200, [
                'user' => $user
            ]);
        } 
        return ApiResponse::error('Credenciales incorrectas', 401);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
