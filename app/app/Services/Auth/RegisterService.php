<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Frontend\User\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class RegisterService
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role,
            ]);

            if($request->role === Role::DRIVER) {
                $user->driverInformation()->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile_phone' => $request->mobile_phone,
                    'location' => random_int(-90, 90) . ','. random_int(-180, 180),
                ]);                
            }

            if($request->role === Role::RIDER) {
                $user->riderInformation()->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mobile_phone' => $request->mobile_phone,
                    'location' => random_int(-90, 90) . ','. random_int(-180, 180),
                ]);                
            }

            $token = $user->createToken('auth-token', ['*'], now()->addDay())->plainTextToken; 
            
            $refreshToken = $user->createToken('refresh-token', ['*'],  now()->addDays(7))->plainTextToken;

            $cookie = cookie("jwt", $token, 60 * 24); // 1 day
            $refreshCookie = cookie("refresh-jwt", $refreshToken, 60 * 24 * 7); // 7 days
    
            return response([
                'user' => new UserResource($user),
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
