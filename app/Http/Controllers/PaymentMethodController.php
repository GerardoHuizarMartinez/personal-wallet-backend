<?php

namespace App\Http\Controllers;

use App\Services\Catalog\PaymentMethodService;
use Illuminate\Http\JsonResponse;

class PaymentMethodController
{
    public function __construct(
        private readonly PaymentMethodService $paymentMethodService
    ) {}

    public function list(): JsonResponse
    {
        return response()->json(
            $this->paymentMethodService->getAll()
        );
    }
}