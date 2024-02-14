<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_can_register(): void
    {
        $faker = Factory::create();
 
        $response = $this->postJson('/api/register', [
            'name' => $faker->name(),
            'email' => $faker->email,
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        //$response->dd();
        $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['user', 'token', 'refreshToken']);
            }
        )
        ->assertCookie('jwt')->assertCookie('refresh-jwt');
    }

    public function test_user_cannot_register_without_data(): void
    {
        $response = $this->postJson('/api/register', []);
        
        //$response->dd();
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
