<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

            return response()->json(['status' => 'success', 'message' => 'Login successful.', 'user' => $user]);
        }

        // Return error if login attempt fails
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials.'], 401);
    }
}
