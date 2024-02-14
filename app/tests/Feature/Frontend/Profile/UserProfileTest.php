<?php

namespace Tests\Feature\Frontend\Profile;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    protected $seeder = UserSeeder::class;

    /**
     * A basic feature test example.
     */
    public function test_user_can_view_profile(): void
    {
        $user = User::where('id', 1)->first();
        $token = $user->createToken('auth-token')->plainTextToken;

        $headers = ['Authorization' => "Bearer $token"];
        $response = $this->getJson('/api/profile', $headers);
        
        $response->dd();
        
    }
}
