<?php

namespace Tests\Feature\Frontend\Rides;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RequestRideTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_rider_user_can_request_a_ride(): void
    {
        $user = User::where('role_id', Role::RIDER)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
      
        $response = $this->postJson('/api/rides', [], $headers);

        //$response->dd();

        $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['message', 'ride', 'driver']);
            }
        );

        $user = User::where('role_id', Role::RIDER)->first();
        $this->assertEquals(1, $user->rides()->count());
    }

    public function test_driver_user_cannot_request_a_ride(): void
    {
        $user = User::where('role_id', Role::DRIVER)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];
      
        $response = $this->postJson('/api/rides', [], $headers);

        //$response->dd();

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['message']);
            }
        );

        $user = User::where('role_id', Role::DRIVER)->first();
        $this->assertEquals(0, $user->rides()->count());
    }
}
