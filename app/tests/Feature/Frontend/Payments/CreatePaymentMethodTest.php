<?php

namespace Tests\Feature\Frontend\Payments;

use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

class CreatePaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_rider_user_can_create_payment_method(): void
    {
        $user = User::where('role_id', Role::RIDER)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $data = [
            "number" => 4242424242424242,
            "cvc" => "789",
            "exp_month" => 12,
            "exp_year" => 2025,
            "card_holder" => "Pedro Pérez"
        ];

        $paymentSourceId = 3344;

        Http::fake([
            // Get acceptence token
            config('laravelDriverApi.wompi.apiUrl') . '/merchants/' . config('laravelDriverApi.wompi.publicKey') => Http::response([
                'data' => [
                    'presigned_acceptance' => [
                        'acceptance_token' => 'eyJhbGciOiJIUzI1NiJ9'
                    ]
                ]
            ], 200),

            // Tokenize card
            config('laravelDriverApi.wompi.apiUrl') . '/tokens/cards' => Http::response([
                'data' => [
                    'id' => 'tok_test_10868_8203A23Ccca988ee40cc3119B982e753'
                ]
            ], 200, [
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.publicKey')
            ]),

            // Create payment source
            config('laravelDriverApi.wompi.apiUrl') . '/payment_sources' => Http::response([
                'data' => [
                    'id' => $paymentSourceId
                ]
            ], 200, [
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.privateKey')
            ]),

        ]);
        
        $response = $this->postJson('/api/payments/payment-method', $data, $headers);
        
        //$response->dd();

        $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson(
            function (AssertableJson $json) {
                $json->hasAll(['message']);
            }
        );

        $user = User::where('role_id', Role::RIDER)->first();
        $this->assertEquals($user->paymentInformation->token_source_id, $paymentSourceId);

    }

    public function test_rider_user_cannot_create_payment_method_with_empty_fields(): void
    {
        $user = User::where('role_id', Role::RIDER)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $data = [];
                
        $response = $this->postJson('/api/payments/payment-method', $data, $headers);
        
        //$response->dd();

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = User::where('role_id', Role::RIDER)->first();
        $this->assertEquals($user->paymentInformation->token_source_id, null);
    }
}
