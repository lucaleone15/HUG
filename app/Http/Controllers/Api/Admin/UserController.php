<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $admins = User::where('is_admin', true)
            ->orderBy('created_at')
            ->get(['id', 'name', 'email', 'created_at']);

        return response()->json($admins);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
        ]);

        return response()->json($user->only(['id', 'name', 'email', 'created_at']), 201);
    }

    public function destroy(int $id, Request $request): Response
    {
        $user = User::where('is_admin', true)->findOrFail($id);

        abort_if($user->id === $request->user()->id, 422, 'Impossible de supprimer votre propre compte.');

        $user->delete();

        return response()->noContent();
    }
}
