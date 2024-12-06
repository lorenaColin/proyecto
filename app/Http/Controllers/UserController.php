<?php

namespace App\Http\Controllers;

use App\Http\Request\UserRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $user->multi_rfc = false;
        $user->status = 'Activo';
        $user->ultima_conexion = now();
        $user->type = 'user';
        $user->save();
        return ApiResponse::success('Usuario registrado con éxito', 201, [
            'user' => $user
        ]);
    }
    public function loginInicio(UserRequest $request)
    {
        $user = User::where('email', $request->email)->first();
    
        if ($user && Hash::check($request->password, $user->password)) {
            
            switch ($user->type) {
                case "adm":
                    $empresas = Company::select('companies.id', 'companies.name')->all();
                    return ApiResponse::success('Datos obtenidos', 200, ['type' => $user->type, 'message' => 'Tienes permisos limitados.', 'id' => $user->id, 'companies' => $empresas ]);
                    break;
                case "user":
                    $empresas = Company::select('companies.id', 'companies.name')->where('id_usr_create', '=', $user->id)->get();
                    return ApiResponse::success('Datos obtenidos', 200, ['type' => $user->type, 'message' => 'Tienes permisos limitados.', 'id' => $user->id, 'companies' => $empresas ]);
                    break;
                default:
                    $empresas = Company::select('companies.id', 'companies.name')
                    ->join('collaborator_company', 'collaborator_company.company_id',  '=', 'companies.id' )
                    ->where('collaborator_company.collaborator_id', '=', $user->id)
                    ->get();
                
                    return ApiResponse::success('Datos obtenidos', 200, ['type' => $user->type, 'message' => 'Tienes permisos limitados.', 'id' => $user->id, 'companies' => $empresas ]);
            }

          
        }
    
        return ApiResponse::error('Credenciales incorrectas', 401);
    }
    
    public function auth(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
      
            if (!$user = User::where('email', $credentials['email'])->first()) {
                return ApiResponse::error('Usuario no encontrado', 404);
            }
    
            if (!Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::error('Contraseña incorrecta', 401);
            }
    
            if (!$token = JWTAuth::fromUser($user)) {
                return ApiResponse::error('No se pudo crear el token', 500);
            }
    
            return ApiResponse::success('Inicio de sesión exitoso', 200, [
                'user' => $user,
                'token' => $token,
                'type' => $user->type
            ]);
           
       
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
