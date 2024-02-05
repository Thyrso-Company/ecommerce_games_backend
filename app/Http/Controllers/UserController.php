<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            $validateData = validator()->make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);

            $validateData = $validateData->validated();

            $user = User::create($validateData);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'user' => $user,
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar usuário',
                'error' => $e->getMessage(),
            ], 405);
        }
    }
}
