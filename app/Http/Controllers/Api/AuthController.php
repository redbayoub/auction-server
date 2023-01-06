<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'alpha_num',
                'min:3',
                function ($attribute, $value, $fail) {
                    if (User::where('username', $value)->exists()) {
                        $fail('The provided ' . $attribute . ' already exists.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->exists()) {
                        $fail('The provided ' . $attribute . ' already exists.');
                    }
                },
            ],
            'password' => 'required|string|min:4',
            'isAdmin' => 'sometimes|required|boolean',
        ]);

        if (
            $request->has('isAdmin') &&
            $request->isAdmin &&
            !(auth()->check() && auth()->user()->isAdmin)
        )
            throw new AuthorizationException();


        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' =>  Hash::make($request->password),
            'isAdmin' => $request->get('isAdmin', false),
        ]);


        return JsonResponse::success(
            'Registered successfully',
            $user,
            Response::HTTP_CREATED
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return JsonResponse::fail(
                'Invalid login details',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken('api')->plainTextToken;

        return JsonResponse::success(
            'logged in successfully',
            ['token' => $token],
            Response::HTTP_OK
        );
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return JsonResponse::success(
            'logged out successfully',
            null,
            Response::HTTP_OK
        );
    }
}
