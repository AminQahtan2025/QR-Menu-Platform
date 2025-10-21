<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Register a new vendor user.
     */
    public function register(Request $request)
    {
        // 1. Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor', // Hardcode role as 'vendor' for this registration
        ]);

        // 3. Create a token for the new user
        $token = $user->createToken('api-token')->plainTextToken;

        // 4. Return the response
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201); // 201 Created
    }

    /**
     * Log the user in and create a token.
     */
    public function login(Request $request)
    {
        // 1. Validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // 3. Authentication was successful, get the user
            $user = Auth::user();

            // 4. Create a token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            // 5. Return the response
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        // 6. Authentication failed
        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401); // 401 Unauthorized
    }

    /**
     * Log the user out (invalidate the token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
