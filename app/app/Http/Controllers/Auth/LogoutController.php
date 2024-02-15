<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LogoutService;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    private $logoutService;

    public function __construct(LogoutService $logoutService)
    {
        $this->logoutService = $logoutService;
    }

    public function index()
    { 
        return $this->logoutService->logout();    
    }
}
