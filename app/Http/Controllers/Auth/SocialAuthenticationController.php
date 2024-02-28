<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

/**
 * @OA\Get(
 *     path="/api/auth/{provider}/callback",
 *     summary="Handle authentication callback from social provider",
 *     @OA\Parameter(
 *         name="provider",
 *         in="path",
 *         required=true,
 *         description="Social provider (e.g., google)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful or user registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLmxvY2FsaG9zdCIsImlhdCI6MTY0Mjk3MzExMiwiZXhwIjoxNjQyOTc2NzEyLCJuYmYiOjE2NDI5NzMxMTIsImp0aSI6IkN0bVBUUWlFbTh1Z3RiNkQiLCJzdWIiOjQ5LCJwcnYiOiI0Y2I4ZjBiYzU0MjQyYmE2NzM5N2UyODdjZDUxYmZjZmM3NzFiMjU0In0.5LxPvHZtq9Wji3pHwNN2cruKwNgrAJMjg2D2U8wct8I")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Failed to authenticate",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Failed to authenticate.")
 *         )
 *     )
 * )
 */

class SocialAuthenticationController extends Controller
{
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to authenticate.'], 401);
        }

        // Check if the user already exists in the database
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token
            ]);
        } else {
            // Extract first and last name
            $name = $this->extractName($socialUser);

            // Automatically register the user and mark the account as verified
            $newUser = User::create([
                'first_name' => $name['first_name'],
                'last_name' => $name['last_name'],
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(str_random(16)), // Generate a random password
                'email_verified_at' => now(), // Mark the account as verified
            ]);

            // Create a customer record for the newly registered user
            $this->createCustomer($newUser);

            // Generate JWT token for the newly registered user
            $token = JWTAuth::fromUser($newUser);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered and logged in successfully.',
                'user' => $newUser,
                'token' => $token
            ], 201);
        }
    }

    // Function to extract first and last name from the provider's response
    private function extractName($socialUser)
    {
        $first_name = null;
        $last_name = null;

        // Extract first name
        if (isset($socialUser->user['given_name'])) {
            $first_name = $socialUser->user['given_name'];
        } elseif (isset($socialUser->user['first_name'])) {
            $first_name = $socialUser->user['first_name'];
        }

        // Extract last name
        if (isset($socialUser->user['family_name'])) {
            $last_name = $socialUser->user['family_name'];
        } elseif (isset($socialUser->user['last_name'])) {
            $last_name = $socialUser->user['last_name'];
        }

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
        ];
    }

    // Method to create a customer record for the user
    protected function createCustomer($user)
    {
        // Create a customer record for the newly verified user
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->save();
    }
}
