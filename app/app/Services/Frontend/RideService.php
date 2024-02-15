<?php
namespace App\Services\Frontend;

use App\Http\Requests\Frontend\Rides\FinishRideRequest;
use App\Models\Ride;
use App\Models\RiderInformation;
use App\Models\RideStatus;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class RideService
{
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role_id !== Role::RIDER) {
                return response([
                    'message' => 'this user cannot request a ride',        
                ], Response::HTTP_UNAUTHORIZED);
            }

            $currentLocation = (random_int(3, 4) + 0.845) . ','. (-1 * random_int(73, 74) + 0.0859);
            $driver = User::where('role_id', Role::DRIVER)->inRandomOrder()->first();

            $ride = $user->rides()->create([
                'initial_location' => $currentLocation,
                'ride_status' => RideStatus::ASSIGNED,
                'driver_id' => $driver->id,
            ]);

            return response([
                'message' => 'ride assigned',
                'ride' => [
                    'currentLocation' => $currentLocation,
                    'ride_id' => $ride->id,
                ],
                'driver' => [
                    'id' => $driver->id,
                    'name' => $driver->driverInformation->first_name . ' ' . $driver->driverInformation->last_name,
                    'email' => $driver->email,
                    'phone' => $driver->driverInformation->mobile_phone
                ]       
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'description' => config('app.debug') ? $e->getMessage()
                    : 'Please activate debug mode to see the error message.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }  
    }

    public function update(FinishRideRequest $request, int $id)
    {
        $user = $request->user();
        if ($user->role_id !== Role::DRIVER) {
            return response([
                'message' => 'this user cannot finish a ride',        
            ], Response::HTTP_UNAUTHORIZED);
        }

        $ride = Ride::where('id', $id)->where('driver_id', $user->id);
        if (!$ride->count()) {
            return response([
                'message' => 'This ride has not been found associated with this user',        
            ], Response::HTTP_NOT_FOUND);
        }

        $ride = Ride::where('id', $id)->where('driver_id', $user->id)->first();
        if ($ride->ride_status === RideStatus::FINISHED) {
            return response([
                'message' => 'This ride has already been finished and payed',        
            ], Response::HTTP_OK);
        }

        // Finish ride
        $ride->update([
            'ride_status' => RideStatus::FINISHED,
            'final_location' => $request->lat . ',' . $request->long
        ]);

        // Get the total price of the ride
        $initialLocationArray = explode(',', $ride->initial_location);
        $iLat = floatval($initialLocationArray[0]);
        $iLong = floatval($initialLocationArray[1]);

        $finalLocationArray = explode(',', $ride->final_location);
        $fLat = floatval($finalLocationArray[0]);
        $fLong = floatval($finalLocationArray[1]);

        $fin = Carbon::parse($ride->updated_at);
        $in = Carbon::parse($ride->created_at);
        $diffInMinutes = $fin->diffInMinutes($in);
        
        $distance = $this->distance($iLat, $iLong, $fLat, $fLong, 'K');
        $distancePrice = $distance * 1000;
        $minutesPrice = $diffInMinutes * 200;
        $baseFeed = 3500;
        $totalPrice = round($distancePrice + $minutesPrice + $baseFeed) * 100;

        // Create transaction
        $riderUser = User::where('id',$ride->user_id)->first();
        $reference = time() . rand(10*45, 100*98);
        $responsePaymentSource = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.privateKey')
        ])
        ->post(config('laravelDriverApi.wompi.apiUrl') . '/transactions', [
            "amount_in_cents" => $totalPrice,
            "currency" => "COP",
            "customer_email" => $riderUser->email,
            "payment_method" => [
                "installments" => 1
            ],
            "payment_source_id" => $riderUser->paymentInformation->token_source_id,
            "reference" => "ref-". $reference ,
            "customer_data" => [
                "phone_number" => $riderUser->riderInformation->mobile_phone,
                "full_name" => $riderUser->riderInformation->first_name . ' ' . $riderUser->riderInformation->last_name 
            ]
        ]);

        
        if (isset($responsePaymentSource->json()['data'])) {
            $transactionId = $responsePaymentSource->json()['data']['id'];
            $transactionStatus = $responsePaymentSource->json()['data']['status'];
          
            return response([
                'message' => 'ride finished',   
                'initial_location' => $ride->initial_location,
                'final_location' => $ride->final_location,
                'distance' => round($distance),
                'totalPrice' => $totalPrice,
                'transactionId' => $transactionId,
                'transactionStatus' => $transactionStatus,
            ], Response::HTTP_OK);
        }

        return response([
            'message' => 'an error occurred',  
            'data' => $responsePaymentSource->json() 
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
      
          if ($unit == "K") {
            return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
          } else {
            return $miles;
          }
        }
    }
}