<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiStatusController extends Controller
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function status()
    {
        try {
            // Attempt to authenticate the user using the JWT token
            if ($user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['status' => 'success', 'message' => 'User is authenticated.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'User is not authenticated.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to authenticate token.'], 500);
        }

    }
}
