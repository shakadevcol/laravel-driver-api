<?php

namespace App\Http\Controllers\Frontend\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Payments\CreatePaymentRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PaymentMethodController extends Controller
{
    public function store(CreatePaymentRequest $request)
    {
        try {
            $user = $request->user();

            // Get acceptence token
            $responseAceptenceToken = Http::get(config('laravelDriverApi.wompi.apiUrl') . '/merchants/' . config('laravelDriverApi.wompi.publicKey'));
    
            $acceptenceToken = $responseAceptenceToken->json()['data']['presigned_acceptance']['acceptance_token'];
            $responseAceptenceToken->json()['data']['presigned_acceptance'];
    
            // Tokenize card
            $responseTokenizeCard = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.publicKey')
            ])
            ->post(config('laravelDriverApi.wompi.apiUrl') . '/tokens/cards', [
                "number" => $request->number,
                "cvc" => $request->cvc,
                "exp_month" => $request->exp_month,
                "exp_year" => $request->exp_year,
                "card_holder" => $request->card_holder,
            ]);
    
            $paymentMethodToken = $responseTokenizeCard->json()['data']['id'];
    
           // Create payment source
            $responsePaymentSource = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('laravelDriverApi.wompi.privateKey')
            ])
            ->post(config('laravelDriverApi.wompi.apiUrl') . '/payment_sources', [
                "customer_email" => $user->email,
                "type" => 'CARD',
                "token" => $paymentMethodToken,
                "acceptance_token" => $acceptenceToken
            ]);
    
            $paymentSourceId = $responsePaymentSource->json()['data']['id'];
            
            $user->paymentInformation()->update([
                'payment_method_id' => PaymentMethod::CARD,
                'token_source_id' => $paymentSourceId
            ]);
    
            return response([
                'message' => 'payment method created'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'description' => config('app.debug') ? $e->getMessage()
                    : 'Please activate debug mode to see the error message.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }    
    }
}
