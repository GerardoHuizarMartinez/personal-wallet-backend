<?php

namespace App\Mappers;

use App\Models\Category;

class CategoryMapper
{
    public static function toArray(Category $category): array
    {
        return [
            'id'     => $category->id,
            'name'   => $category->name,
            'label'  => $category->label,
            'type'   => $category->type,
            'icon'   => $category->icon,
            'status' => $category->status,
        ];
    }

    public static function collection(iterable $categories): array
    {
        return collect($categories)
            ->map(fn(Category $category) => self::toArray($category))
            ->values()
            ->all();
    }
}