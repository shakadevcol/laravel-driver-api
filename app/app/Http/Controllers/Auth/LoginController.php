<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Frontend\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(LoginRequest $request): Response
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'invalid login credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth-token', ['*'], now()->addDay())->plainTextToken; 
        $refreshToken = $user->createToken('refresh-token', ['*'],  now()->addDays(7))->plainTextToken;

        $cookie = cookie("jwt", $token, 60 * 24); // 1 day
        $refreshCookie = cookie("refresh-jwt", $refreshToken, 60 * 24 * 7); // 7 days
     
        return response([
            'user' => new UserResource($user),
            'token' => $token,
            'refreshToken' => $refreshToken,
        ])->withCookie($cookie)->withCookie($refreshCookie);
    }
}
