<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Mappers\IncomeMapper;
use App\Models\Income;
use App\Services\IncomeService;

class IncomeController
{
    public function __construct(
        private readonly IncomeService $incomeService
    ) {}


    public function list(Request $request)
    {

        $currentDate = Carbon::now();

        $startdate = $request->filled('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::create($currentDate->year, $currentDate->month, 1)->startOfDay();

        $enddate = $request->filled('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : $currentDate->copy()->endOfDay();

        $list = Income::select(
            'id',
            'product_name',
            'amount',
            'user_id',
            'category_id',
            'payment_method_id',
            'income_date',
            'comments'
        )
            ->with([
                'category:id,name',
                'payment_method:id,name,label',
            ])
            ->whereBetween('income_date', [$startdate, $enddate])
            ->where('user_id', 1)
            ->orderByDesc('id')
            ->paginate(200); // ← ¿tienes esto?

        // Y el summary se calcula sobre TODOS los registros, no solo la página
        $allList = Income::whereBetween('income_date', [$startdate, $enddate])
            ->where('user_id', 1)
            ->get(['amount']);

        $summaryPaymentMethod = $allList
            ->groupBy('financing_method')
            ->map(function ($items, $method) {
                return [
                    'method' => $method,
                    'total_count' => $items->count(),
                    'total_sum' => round((float) $items->sum('total_price'), 2),
                ];
            })
            ->values();

        return response()->json([
            'data' => $list->items(),        // ← items() no get()
            'grouped' => $summaryPaymentMethod,
            'current_date' => $currentDate,
            'total' => $list->total(),
            'current_page' => $list->currentPage(),
            'last_page' => $list->lastPage(),
            'status' => 200,
        ]);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'amount'            => 'required|numeric|min:0.01',
            'category_id'       => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'income_date'       => 'required|date',
            'comments'          => 'nullable|string|max:250',
        ]);

        $expense = $this->incomeService->create($validated);

        return response()->json($expense, 201);
    }
 

    public function update($id, Request $request)
    {
        try {

            $validated = $request->validate([
                'product_name'      => 'required|string',
                'amount'            => 'required|numeric',
                'category_id'       => 'required|numeric',
                'payment_method_id' => 'required|numeric',
                'income_date'       => 'required|date',
                'comments'          => 'nullable|string',
            ]);

            $income = Income::findOrFail($id);
            $income->update($validated);
            $income->load('category', 'payment_method'); // ← carga relaciones


            return response()->json(
                IncomeMapper::toDashboard(collect([$income]))->first()
            ); 
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {

            $income = Income::findOrFail($id);
            $income->delete();

            return response()->json([
                'message' => 'Ingreso eliminado correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
