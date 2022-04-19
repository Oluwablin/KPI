<?php

namespace App\Http\Controllers\v1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->only('firstname', 'lastname', 'email', 'password', "role");

        $data["password"] =  Hash::make($request->password);
        $role = $data["role"];
        unset($data["role"]);
        try {
            $user = User::create($data);

            if (isset($role) && $role === "admin") {
                $userRole = User::isAdmin;
            } else {
                $userRole = User::isEmployee;
            }

            if ($userRole) {
                $user->attachRole($userRole);
            }

            if ($user->is_verified == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account already verified.',
                    'data' => null
                ], 400);
            }

            $user->update(['is_verified' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful.',
                'data' => null
            ], 201);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
                'data' => null
            ]);
        }
    }
    
}
