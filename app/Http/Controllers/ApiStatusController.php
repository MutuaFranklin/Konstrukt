<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiStatusController extends Controller
{
    public function status()
    {
        return response()->json(['status' => 'API is running'], 200);
    }
}
