<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshTokenController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->tokens()->delete();

        //$token = $user->createToken('auth-token', ['*'], now()->addDay())->plainTextToken; 
        $token = $user->createToken('auth-token', ['*'], now()->addMinute())->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['*'],  now()->addDays(7))->plainTextToken;

        //$cookie = cookie("jwt", $token, 60 * 24); // 1 day
        $cookie = cookie("jwt", $token, 1); // 1 minute
        $refreshCookie = cookie("refresh-jwt", $refreshToken, 60 * 24 * 7); // 7 days

        return response([
            'message' => 'generate new tokens for ' . $user->name,
            'token' => $token,
            'refreshToken' => $refreshToken,
        ])->withCookie($cookie)->withCookie($refreshCookie);
    }
}
