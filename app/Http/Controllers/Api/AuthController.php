<?php

namespace App\Http\Controllers\Api;

use App\Enums\Auth\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    //register
     public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $token = $user->createToken('ecommerce-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }


    //login
    public function login(LoginRequest $request)
{
    $credentials = $request->validated();

    if (! Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }

    $user = Auth::user();

    if ($user->status !== UserStatus::ACTIVE) {
        return response()->json([
            'success' => false,
            'message' => 'Your account is inactive or suspended.',
        ], 403);
    }

    $token = $user->createToken('ecommerce-api')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login successful.',
        'data' => [
            'user' => new UserResource($user),
            'token' => $token,
        ],
    ]);
}
}
