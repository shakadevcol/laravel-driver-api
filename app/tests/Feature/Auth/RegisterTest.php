<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
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

    protected $seed = true;
    protected $seeder = RoleSeeder::class;


    public function test_driver_user_can_register(): void
    {
        $faker = Factory::create();
 
        $response = $this->postJson('/api/register', [
            'email' => $faker->email,
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => Role::DRIVER,
            'first_name' => $faker->firstName(),
            'last_name' =>  $faker->lastName(),
            'mobile_phone' => $faker->phoneNumber(),
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

    public function test_rider_user_can_register(): void
    {
        $faker = Factory::create();
 
        $response = $this->postJson('/api/register', [
            'email' => $faker->email,
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => Role::RIDER,
            'first_name' => $faker->firstName(),
            'last_name' =>  $faker->lastName(),
            'mobile_phone' => $faker->phoneNumber(),
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
