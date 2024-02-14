<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    protected $seeder = UserSeeder::class;

    public function test_user_can_refresh_token(): void
    {
        // Login request
        $user = User::where('id', 1)->first();
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123456'
        ]);

        //$response->dd();
        //$response->dumpHeaders();
        //$response->ddHeaders();

        $cookie = $response->headers->get('Set-Cookie');
        $cookieAr = explode(";", $cookie); 
        $jwt = explode("=", $cookieAr[0]);
        $token = $jwt[1];
       
        // Refresh token request
        $response = $this->withCredentials()
        ->withCookie('refresh-jwt', $token)
        ->postJson('/api/refresh-token', []);

        //$response->dd();
        $response->assertStatus(Response::HTTP_OK)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['message', 'token', 'refreshToken']);
            }
        )
        ->assertCookie('jwt')->assertCookie('refresh-jwt');
    }

    public function test_user_cannot_refresh_token_with_invalid_token(): void
    {
        $token = "abc123";  
        $response = $this->withCredentials()
        ->withCookie('refresh-jwt', $token)
        ->postJson('/api/refresh-token', []);

        //$response->dd();
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_cannot_refresh_token_without_token(): void
    { 
        $response = $this->withCredentials()
        ->postJson('/api/refresh-token', []);

        //$response->dd();
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
