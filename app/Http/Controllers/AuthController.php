<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken($request->email)->plainTextToken;

            activity('Login')
                ->performedOn($request->user())
                ->withProperties([
                    'attributes'=> $request->user(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('Login Successful');

            return response()->json([
                'message' => 'Login bem-sucedido',
                'token' => $token,
                'userId' => $request->user()->id,
                'userName' => $request->user()->name,
            ], 200);
        }

        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

    public function register(Request $request)
    {
        try {

            $validatedData = validator()->make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $validatedData = $validatedData->validated();

            $user = User::create($validatedData);

            return response()->json([
                'message' => 'Usuário registrado com sucesso',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar usuário',
                'error' => $e->getMessage(),
            ], 405);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Logout bem-sucedido'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao fazer logout',
                'error' => $e->getMessage(),
            ], 405);
        }

    }
}
