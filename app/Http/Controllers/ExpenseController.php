<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Mappers\ExpenseMapper;


class ExpenseController
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {}


    public function list()
    {

        $currentDate = Carbon::now();
        $fechaFormateada = $currentDate->format('Y-m-d H:i:s');
        $month  = $currentDate->month;
        $year = $currentDate->year;

        $startdate = Carbon::create($year, $month, 1)->startOfDay();
        $enddate = Carbon::parse($fechaFormateada)->endOfDay();

        $list = Expense::select(
            'id',
            'product_name',
            'amount',
            'user_id',
            'category_id',
            'payment_method_id',
            'expense_date',
            'comments'
        )
            ->with([
                'category:id,name',
                'payment_method:id,name,slug',
            ])
            ->whereBetween('expense_date', [$startdate, $enddate])
            ->where('user_id', 1)
            ->orderByDesc('id')
            ->paginate(200); // ← ¿tienes esto?

        // Y el summary se calcula sobre TODOS los registros, no solo la página
        $allList = Expense::whereBetween('expense_date', [$startdate, $enddate])
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
            'expense_date'      => 'required|date',
            'comments'          => 'nullable|string|max:250',
        ]);

        $expense = $this->expenseService->create($validated);

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
                'expense_date'      => 'required|date',
                'comments'          => 'nullable|string',
            ]);

            $expense = Expense::findOrFail($id);
            $expense->update($validated);
            $expense->load('category', 'payment_method'); // ← carga relaciones


            return response()->json(
                ExpenseMapper::toDashboard(collect([$expense]))->first()
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

            $expense = Expense::findOrFail($id);
            $expense->delete();

            return response()->json([
                'message' => 'Gasto eliminado correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
