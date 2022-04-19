<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => true,
                'message' => 'Incorrect email or password',
                'data' => null
            ]);
        }
        // Data to return
        $data = [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
        ];

        return response()->json([
            'error' => false,
            'message' => 'You are logged in successfully',
            'data' => $data
        ]);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'error' => false,
            'message' => 'Successfully logged out',
            'data' => null
        ]);
    }
}
