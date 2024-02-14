<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;

class RegisterController extends Controller
{
    public function index(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            //$token = $user->createToken('auth-token', ['*'], now()->addDay())->plainTextToken; 
            $token = $user->createToken('auth-token', ['*'], now()->addMinute())->plainTextToken;
            $refreshToken = $user->createToken('refresh-token', ['*'],  now()->addDays(7))->plainTextToken;

            //$cookie = cookie("jwt", $token, 60 * 24); // 1 day
            $cookie = cookie("jwt", $token, 1); // 1 minute
            $refreshCookie = cookie("refresh-jwt", $refreshToken, 60 * 24 * 7); // 7 days
    
            return response([
                'user' =>  $user,
                'token' => $token,
                'refreshToken' => $refreshToken,
            ], Response::HTTP_CREATED)
            ->withCookie($cookie)->withCookie($refreshCookie);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'description' => config('app.debug') ? $e->getMessage()
                    : 'Please activate debug mode to see the error message.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }    
    }
}
