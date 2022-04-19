<?php

namespace App\Http\Controllers\v1\Auth;

use App\Models\User;
//use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
//use App\Http\Requests\ResendCodeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
//use JWTAuth;
// use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Str;

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

            // $verification_code = Str::random(30); //Generate verification code
            // DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);

            // $maildata = [
            //     'email' => $data['email'],
            //     'name' => $data["firstname"],
            //     'verification_code' => $verification_code,
            //     'subject' => "Please verify your email address.",
            // ];

            // Mail::to($data['email'])->send(new VerifyEmail($maildata));
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

    /**
     * Resend Email Token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function resendCode(ResendCodeRequest $request)
    // {
    //     $email = $request->email;
    //     $user = User::where("email", $email)->first();
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User not found',
    //             'data' => null
    //         ], 404);
    //     }

    //     if ($user->is_verified) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Account already verified',
    //             'data' => null
    //         ], 400);
    //     }

    //     $verification_code = Str::random(30); //Generate verification code
    //     DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);

    //     $maildata = [
    //         'email' => $email,
    //         'name' => $user->firstname,
    //         'verification_code' => $verification_code,
    //         'subject' => "Please verify your email address.",
    //     ];

    //     Mail::to($email)->send(new VerifyEmail($maildata));
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Verification link sent successfully.',
    //         'data' => null
    //     ], 200);
    // }
}
