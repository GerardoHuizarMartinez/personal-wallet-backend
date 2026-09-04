<?php

namespace App\Http\Controllers;

use App\Services\ColonyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ColonyController
{
    public function __construct(
        private readonly ColonyService $colonyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->colonyService->search($request->query('search')));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->colonyService->find($id));
    }
}