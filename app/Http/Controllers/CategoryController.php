<?php

namespace App\Http\Controllers;

use App\Services\Catalog\CategoryService;
use Illuminate\Http\JsonResponse;


class CategoryController 
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function listExpense(): JsonResponse
    {
        return response()->json(
            $this->categoryService->getExpenseCategories()
        );
    }

    public function listIncome(): JsonResponse
    {
        return response()->json(
            $this->categoryService->getIncomeCategories()
        );
    }
}