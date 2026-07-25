<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Models\Expense;

class ExpenseController
{
    /**
     * Display a listing of the resource.
     */


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
            ->whereBetween('expense_date', ["2025-07-01", "2025-07-31"])
            ->where('user_id', 1)
            ->orderByDesc('id')
            ->paginate(200); // ← ¿tienes esto?

        // Y el summary se calcula sobre TODOS los registros, no solo la página
        $allList = Expense::whereBetween('expense_date', ["2025-07-01", "2025-07-31"])
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

    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $validate = $request->validate([
                'product_name'       => 'required|string',
                'amount'        => 'required|numeric',
                'user_id'            => 'required|numeric',
                'category_id'        => 'required|numeric',
                'payment_method_id'  => 'required|numeric',
                'expense_date'      => 'required|date',
                'comments'           => 'nullable|string'
            ]);

            Expense::create([
                'product_name' => $request->input('product_name'),
                'amount' => $request->input('amount'),
                'user_id' => $request->input('user_id'),
                'category_id' => $request->input('category_id'),
                'payment_method_id' => $request->input('payment_method_id'),
                'expense_date' => $request->input('expense_date'),
                'comments' => $request->input('comments'),
            ]);


            return response()->json([
                'status' => 200,
                'message' => 'Compra registrada correctamente',
                'data' => $validate,
            ], 200);
        } catch (ValidationException $e) {
            // Retornar error 422 con detalles de validación
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors(),  // Aquí están los mensajes por campo
            ], 422);
        } catch (Exception $e) {
            // Retornar error 422 con detalles de validación
            return response()->json([
                'status' => 'error',
                'message' => 'Error ',
                'errors' => $e,  // Aquí están los mensajes por campo
            ], 400);
        }
    }


    public function update($id, Request $request)
    {
        try {

            $validate = $request->validate([
                'product_name'       => 'required|string',
                'amount'        => 'required|numeric',
                'category_id'        => 'required|numeric',
                'payment_method_id'  => 'required|numeric',
                'expense_date'      => 'required|date',
                'comments'           => 'nullable|string'
            ]);

            // Buscar el expense con su purchase
            $expense = Expense::find($id)->get();


            // Actualizar expense con datos del request
            $expense->update([
                'product_name' => $validate['product_name'],
                'amount' => $validate['amount'],
                'category_id' => $validate['category_id'],
                'payment_method_id' => $validate['payment_method_id'],
                'expense_date' => $validate['expense_date'],
                'comments' => $validate['comments'],
            ]);


            $expense->refresh(); // mismo que fresh()
            $expense->load('paymentMethod', 'category');

            return response()->json([
                'status' => 200,
                'message' => 'Registro actualizado correctamente',
                'expense' => $expense,
                'purchase' => $expense->purchase,
            ]);
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
    public function destroy($expenseId)
    {
        try {
            $expense = Expense::find($expenseId);

            if ($expense->purchase) {
                $expense->delete();
                $expense->purchase->delete();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Registro eliminado correctamente',
                'pur' => $expense,
                // 'exp' => $expense,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e,
            ]);
        }
    }
}
