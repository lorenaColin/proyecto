<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['ultima_conexion'] = now();
        $validatedData['code'] = strtoupper(Str::random(4));
        $user = User::create($validatedData);
        $token = JWTAuth::fromUser($user);
        $this->sendVerificationEmail($user);
        return ApiResponse::success('Registro exitoso. Se ha enviado un código de verificación a tu correo electrónico.', 200, [
            'user' => $user,
            'token' => $token
        ]);
    }
    private function sendVerificationEmail(User $user)
    {
        try {
            $token = JWTAuth::fromUser($user);
            Mail::to($user->email)->send(new \App\Mail\UserVerificationMail($user->name, $user->code, $token));
        } catch (\Exception $e) {
            throw new \Exception('Error al enviar el correo de verificación.');
        }
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = JWTAuth::fromUser($user);
             //Obtiene la primer compañia
             $companyUuid = null;
             $company = $user->companies()->first();
             if ($company) {
                 $companyUuid = $company->id;
             }
             $customClaims = [
                 'company_uuid' => $companyUuid,
             ];
             $token = JWTAuth::claims($customClaims)->fromUser($user);
            User::where('id', $user->id)->update(['ultima_conexion'=> now()]);
            return ApiResponse::success('Inicio de sesión exitoso', 200, [
                'type' => $user->type,
                'verified' => $user->email_verified_at ?? '',
                'token' => $token
            ]);
        } 
        return ApiResponse::error('Credenciales incorrectas', 401);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Sesión cerrada con exito']);
    }


    public function verifyCode(UserRequest $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return ApiResponse::error('Usuario no autenticado o token inválido.', 401);
            }
        } catch (JWTException $e) {
            return ApiResponse::error('Token no válido.', 401);
        }
        if ($user->code !== $request->code) {
            return ApiResponse::error('El código de verificación es incorrecto.', 400);
        }

        if ($user->email_verified_at) {
            return ApiResponse::success('El correo ya está verificado.', 200, []);
        }
        
        if ($user instanceof User) {
            User::where('id', $user->id)->update(['email_verified_at'=> now()]);
        }

        return ApiResponse::success('Correo verificado con éxito.', 200, [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'email_verified_at' => $user->email_verified_at,
            ]
        ]);
    }
    
    /**
     * Display the specified resource.
     */
    public function resendcode()
    {
        $user = auth('api')->user();

        if (!$user) {
            return ApiResponse::error('Usuario no autenticado o token inválido.', 401);
        }

        if ($user->email_verified_at) {
            return ApiResponse::error('El correo ya ha sido verificado.', 400);
        }

        $this->sendVerificationEmail($user);

        return ApiResponse::success('Se ha reenviado un código de verificación a tu correo electrónico.', 200);
    }

    // public function refreshToken(Request $request)
    // {
    //     try {
    //         $newToken = JWTAuth::refresh(JWTAuth::getToken()); // Renueva el token
    //         return response()->json(['token' => $newToken], 200);
    //     } catch (JWTException $e) {
    //         return response()->json(['error' => 'Could not refresh token'], 401);
    //     }
    // }

    // public function respondWithToken($token)
    // {
    //     try {
    //         return response()->json([
    //             'access_token' => $token,
    //             'token_type' => 'bearer',
    //             'expires_in' => auth()->factory()->getTTL() * 60
    //         ]);
    //     } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => 'El token ha caducado y ya no se puede actualizar'
    //         ]);
    //     }
        

        /*
        try {
            if (!JWTAuth::getToken()) {
                return ApiResponse::error('Token no propórcionado.', 401);
            }
            $currentToken = JWTAuth::getToken();
            JWTAuth::setToken($currentToken);

            $payload = JWTAuth::checkOrFail(); 
            $newToken = JWTAuth::refresh($currentToken);

            return ApiResponse::success('Se ha reenviado un código de verificación a tu correo electrónico.', 200, [
                'message' => 'Token renovado con éxito',
                'token' => $newToken
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token invalido.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Token ha expirado y no puede ser refrescado.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Error con el token', 500, $e->getMessage());
        }
            */
    // }

    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken()); // Renueva el token
            return response()->json(['token' => $newToken], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }
 

}
