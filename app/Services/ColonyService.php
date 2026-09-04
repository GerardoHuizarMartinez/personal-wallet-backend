<?php

namespace App\Services;

use App\Models\Colony;
use App\Mappers\ColonyMapper;

class ColonyService
{
    public function search(?string $query): array
    {
        $colonies = Colony::query()
            ->when($query, fn($builder) => $builder->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('zipcode', 'like', "{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            }))
            ->orderBy('name')
            ->limit(30)
            ->get();

        return ColonyMapper::collection($colonies);
    }

    public function find(int $id): array
    {
        return ColonyMapper::toArray(Colony::findOrFail($id));
    }
}