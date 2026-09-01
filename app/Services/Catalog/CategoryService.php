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

    public function update(int $id, array $data): array
    {
        $category = Category::findOrFail($id);

        $category->update([
            'name'   => $data['name'],
            'label'  => $data['label'],
            'type'   => $data['type'],
            'status' => $data['status'],
        ]);

        return CategoryMapper::toArray($category);
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