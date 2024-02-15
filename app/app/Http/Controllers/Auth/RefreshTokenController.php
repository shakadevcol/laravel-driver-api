<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\RefreshTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshTokenController extends Controller
{
    private $refreshTokenService;

    public function __construct(RefreshTokenService $refreshTokenService)
    {
        $this->refreshTokenService = $refreshTokenService;
    }

    public function index(Request $request): Response
    {
        return $this->refreshTokenService->refresh($request);
    }
}
