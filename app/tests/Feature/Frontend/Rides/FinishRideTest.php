<?php

namespace Tests\Feature\Frontend\Rides;

use App\Models\PaymentMethod;
use App\Models\RiderInformation;
use App\Models\RideStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FinishRideTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_driver_user_can_finish_a_ride(): void
    {
        $riderUser = User::where('role_id', Role::RIDER)->first();
        $driverUser = User::where('role_id', Role::DRIVER)->first();
             
        // Rider adds payment method
        $riderUser->paymentInformation()->update([
            'payment_method_id' => PaymentMethod::CARD,
            'token_source_id' => 3344
        ]);

        // rider request a ride
        $ride = $riderUser->rides()->create([
            'initial_location' => '4.85472719678535,-74.03006840293912',
            'ride_status' => RideStatus::ASSIGNED,
            'driver_id' => $driverUser->id,
        ]);

        // driver finish a ride
        $token = $driverUser->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
      
        $data = [
            'lat' => 4.657629487429691,
            'long' => -74.06202734870323
        ];

        Http::fake([
            // Create transaction
            config('laravelDriverApi.wompi.apiUrl') . '/transactions' => Http::response([
                'data' => [
                    'id' => '110868-1707992657-40654',
                    'status' => 'PENDING'
                ]
            ], 200, [
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.privateKey')
            ]),
        ]);

        $response = $this->putJson('/api/rides/' . $ride->id , $data, $headers);

        //$response->dd();

        $response->assertStatus(Response::HTTP_OK)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll([
                'message',   
                'initial_location',
                'final_location',
                'distance',
                'totalPrice',
                'transactionId',
                'transactionStatus']);
            }
        );

        $this->assertEquals(RideStatus::FINISHED, $ride->refresh()->ride_status);
    }

    public function test_driver_user_cannot_finish_a_ride_with_empty_location(): void
    {
        $riderUser = User::where('role_id', Role::RIDER)->first();
        $driverUser = User::where('role_id', Role::DRIVER)->first();
             
        // Rider adds payment method
        $riderUser->paymentInformation()->update([
            'payment_method_id' => PaymentMethod::CARD,
            'token_source_id' => 3344
        ]);

        // rider request a ride
        $ride = $riderUser->rides()->create([
            'initial_location' => '4.85472719678535,-74.03006840293912',
            'ride_status' => RideStatus::ASSIGNED,
            'driver_id' => $driverUser->id,
        ]);

        // driver finish a ride
        $token = $driverUser->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
      
        $data = [];

        Http::fake([
            // Create transaction
            config('laravelDriverApi.wompi.apiUrl') . '/transactions' => Http::response([
                'data' => [
                    'id' => '110868-1707992657-40654',
                    'status' => 'PENDING'
                ]
            ], 200, [
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.privateKey')
            ]),
        ]);

        $response = $this->putJson('/api/rides/' . $ride->id , $data, $headers);

        // $response->dd();

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals(RideStatus::ASSIGNED, $ride->refresh()->ride_status);
    }
}
