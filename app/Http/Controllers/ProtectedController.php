<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProtectedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function protectedData()
    {
        return response()->json([
            'message' => 'Это защищенные данные!',
            'user' => auth()->user(),
            'data' => ['secret' => 'Confidential information']
        ]);
    }

    public function adminData()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'message' => 'Админские данные',
            'data' => ['admin_secret' => 'Super secret admin data']
        ]);
    }
}