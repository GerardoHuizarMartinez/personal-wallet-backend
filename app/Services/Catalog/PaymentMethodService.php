<?php

namespace App\Services\Catalog;

use App\Models\PaymentMethod;
use App\Mappers\PaymentMethodMapper;

class PaymentMethodService
{
    public function getAll(): array
    {
        $methods = PaymentMethod::query()
            ->select('id', 'name', 'label', 'icon')
            ->orderBy('id')
            ->get();

        return PaymentMethodMapper::collection($methods);
    }
}