<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshTokenService
{
    public function refresh(Request $request): Response
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth-token', ['*'], now()->addDay())->plainTextToken; 
        $refreshToken = $user->createToken('refresh-token', ['*'],  now()->addDays(7))->plainTextToken;

        $cookie = cookie("jwt", $token, 60 * 24); // 1 day
        $refreshCookie = cookie("refresh-jwt", $refreshToken, 60 * 24 * 7); // 7 days

        return response([
            'message' => 'generate new tokens for ' . $user->name,
            'token' => $token,
            'refreshToken' => $refreshToken,
        ])->withCookie($cookie)->withCookie($refreshCookie);
    }
}
