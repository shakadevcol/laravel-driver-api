<?php

namespace App\Http\Controllers\Frontend\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Payments\CreatePaymentRequest;
use App\Services\Frontend\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentMethodController extends Controller
{
    private $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;    
    }
    
    public function store(CreatePaymentRequest $request)
    {
        return $this->paymentMethodService->store($request);
    }
}
