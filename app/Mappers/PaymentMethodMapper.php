<?php

namespace App\Mappers;

use App\Models\PaymentMethod;

class PaymentMethodMapper
{
    public static function toArray(PaymentMethod $paymentMethod): array
    {
        return [
            'id'    => $paymentMethod->id,
            'name'  => $paymentMethod->name,
            'label' => $paymentMethod->label,
            'icon'  => $paymentMethod->icon,
        ];
    }

    public static function collection(iterable $paymentMethods): array
    {
        return collect($paymentMethods)
            ->map(fn(PaymentMethod $pm) => self::toArray($pm))
            ->values()
            ->all();
    }
}