<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    protected $seeder = UserSeeder::class;

    public function test_user_can_login_with_credentials(): void
    {
        $user = User::where('id', 1)->first();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123456'
        ]);

        //$response->dd();
        $response->assertStatus(Response::HTTP_OK)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['user', 'token', 'refreshToken']);
            }
        )
        ->assertCookie('jwt')->assertCookie('refresh-jwt');
    }

    public function test_user_cannot_login_without_credentials(): void
    {
        $response = $this->postJson('/api/login', []);

        //$response->dd();
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
