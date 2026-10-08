<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        // Implement your registration logic here
        $user = $this->authService->register($request->validated());

        return response()->json(['user' => $user], 201);
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function logout(Request $request)
    {
        // Implement your logout logic here
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }
}
