<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_user_can_logout(): void
    {
        $response = $this->postJson('/api/logout', []);

        //$response->dd();
        $response->assertStatus(Response::HTTP_OK)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['message']);
            }
        )
        ->assertCookieExpired('jwt')->assertCookieExpired('refresh-jwt');
    }
}
