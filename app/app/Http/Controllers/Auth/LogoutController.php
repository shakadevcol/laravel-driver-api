<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function index()
    { 
        $cookie = Cookie::forget('jwt');
        $refreshCookie = Cookie::forget('refresh-jwt');

        return response([
            'message' => 'Logout completed.'
        ])->withCookie($cookie)->withCookie($refreshCookie);
    }
}
