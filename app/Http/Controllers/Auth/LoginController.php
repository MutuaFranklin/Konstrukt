<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Handle a login request",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="User's email address"),
 *             @OA\Property(property="password", type="string", format="password", example="password123", description="User's password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLmxvY2FsaG9zdCIsImlhdCI6MTY0Mjk3MzExMiwiZXhwIjoxNjQyOTc2NzEyLCJuYmYiOjE2NDI5NzMxMTIsImp0aSI6IkN0bVBUUWlFbTh1Z3RiNkQiLCJzdWIiOjQ5LCJwcnYiOiI0Y2I4ZjBiYzU0MjQyYmE2NzM5N2UyODdjZDUxYmZjZmM3NzFiMjU0In0.5LxPvHZtq9Wji3pHwNN2cruKwNgrAJMjg2D2U8wct8I")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials or email not verified",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Invalid credentials or email not verified.")
 *         )
 *     )
 * )
 */

class LoginController extends Controller
{
    # Handle a login request to the application.

    public function login(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        // If validation fails, return the validation errors as JSON response
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        // Attempt to log in the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user's email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return response()->json(['status' => 'error', 'message' => 'Email not verified.'], 401);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json(['status' => 'success', 'message' => 'Login successful.', 'user' => $user, 'token' => $token]);
        }

        // Return error if login attempt fails
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials.'], 401);
    }
}
