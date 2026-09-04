<?php

namespace App\Mappers;

use App\Models\Colony;

class ColonyMapper
{
    public static function toArray(Colony $colony): array
    {
        return [
            'id'              => $colony->id,
            'name'            => $colony->name,
            'zipcode'         => $colony->zipcode,
            'settlement_type' => $colony->settlement_type,
            'city'            => $colony->city,
            'state'           => $colony->state,
        ];
    }

    public static function collection(iterable $colonies): array
    {
        return collect($colonies)
            ->map(fn(Colony $colony) => self::toArray($colony))
            ->values()
            ->all();
    }
}