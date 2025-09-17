<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtectedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'message' => 'Welcome to dashboard!',
            'user' => auth()->user(),
            'data' => ['secret' => 'This is protected data']
        ]);
    }

    public function profile(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'user' => $user,
            'profile_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'email_verified' => !is_null($user->email_verified_at),
                'member_since' => $user->created_at->diffForHumans()
            ]
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}