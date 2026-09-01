<?php

namespace App\Http\Controllers;

use App\Services\Catalog\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:50',
            'label'  => 'nullable|string|max:30',
            'type'   => 'required|in:expense,income',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category = $this->categoryService->create($validated);

        return response()->json($category, 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:50',
            'label'  => 'required|string|max:30',
            'type'   => 'required|in:expense,income',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category = $this->categoryService->update($id, $validated);

        return response()->json($category);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->delete($id);

        return response()->json([
            'message' => 'Categoría eliminada correctamente',
        ]);
    }
}