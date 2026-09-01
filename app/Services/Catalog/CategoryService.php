<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Mappers\CategoryMapper; 

class CategoryService
{
    public function getExpenseCategories(): array
    {
        return CategoryMapper::collection(
            $this->getByType('expense')
        );
    }

    public function getIncomeCategories(): array
    {
        return CategoryMapper::collection(
            $this->getByType('income')
        );
    }

    private function getByType(string $type)
    {
        return Category::query()
            ->select('id', 'name', 'label', 'icon', 'type', 'status')
            ->where('type', $type)
            ->orderBy('name')
            ->get();
    }
}