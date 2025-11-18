<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register method
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // generate token (authorization)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // get user with that email
        $user = User::where('email', $request->email)->first();

        if (!$user ||!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These credentials are not valid'
            ]);
        };

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    // get authenticated user
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    // logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'succes' => true,
            'message' => 'User logged out successfully'
        ]);
    }
}
