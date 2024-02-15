<?php

namespace App\Http\Controllers\Frontend\Rides;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Rides\FinishRideRequest;
use App\Models\Ride;
use App\Models\RideStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\Frontend\RideService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class RideController extends Controller
{
    private $rideService;

    public function __construct(RideService $rideService)
    {
        $this->rideService = $rideService;    
    }

    public function store(Request $request)
    {
        return $this->rideService->store($request);
    }

    public function update(FinishRideRequest $request, int $id)
    {
        return $this->rideService->update($request, $id);
    }
}
