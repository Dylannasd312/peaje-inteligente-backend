<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'mensaje' => 'Credenciales incorrectas.'
            ], 401);
        }

        if (!$usuario->activo) {
            return response()->json([
                'mensaje' => 'La cuenta está desactivada.'
            ], 403);
        }

        $token = $usuario->createToken('peaje-app')->plainTextToken;

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso.',
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'email' => $usuario->email,
                'rol' => $usuario->role,
            ],
            'token' => $token,
            'tipo_token' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente.'
        ]);
    }

    public function usuario(Request $request)
    {
        return response()->json([
            'usuario' => $request->user()
        ]);
    }
}
