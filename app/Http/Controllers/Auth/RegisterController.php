<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\Customer;


class RegisterController extends Controller
{
    #Register a new user.
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // If validation fails, return the validation errors as JSON response
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // Create a new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch email verification notification
        $user->sendEmailVerificationNotification();

    
        // Return JSON response with success message
        return response()->json(['status' => 'success', 'message' => 'Registration successful! Please verify your email.']);
    }


    # Create a customer record for the given user.
    protected function createCustomer(User $user)
    {
        // Create a customer record for the newly registered user
        Customer::create([
            'user_id' => $user->id,
        ]);
    }
}
