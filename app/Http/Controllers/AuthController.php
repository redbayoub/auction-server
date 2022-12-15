<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:4',
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            return JsonResponse::fail(
                'Invalid login details',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $request->session()->regenerate();

        return JsonResponse::success(
            'logged in successfully',
            null,
            Response::HTTP_OK
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return JsonResponse::success(
            'logged out successfully',
            null,
            Response::HTTP_OK
        );
    }
}
