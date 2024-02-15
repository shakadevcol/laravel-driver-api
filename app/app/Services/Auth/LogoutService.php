<?php

namespace App\Services\Auth;


use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class LogoutService
{
    public function logout()
    { 
        $cookie = Cookie::forget('jwt');
        $refreshCookie = Cookie::forget('refresh-jwt');

        return response([
            'message' => 'Logout completed.'
        ])->withCookie($cookie)->withCookie($refreshCookie);
    }
}
