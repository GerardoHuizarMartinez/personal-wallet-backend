<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Expense;
use Exception;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function list()
    {
        $currentDate = Carbon::now();
        $fechaFormateada = $currentDate->format('Y-m-d H:i:s');
        $mes  = $currentDate->month;
        $anio = $currentDate->year;

        $fechaInicio = Carbon::create($anio, $mes, 1)->startOfDay();
        $fechaFin = Carbon::parse($fechaFormateada)->endOfDay();
        // dd($fechaFin);

        $list = Purchase::select('id', 'product_name', 'total_price', 'purchase_date', 'category_id', 'financing_method', 'comments', 'payment_method_id')
            ->with([
                'category:id,name,icon',
                'payment_method:id,name,icon',
                'expenses' => function ($query) use ($fechaInicio, $fechaFin) {
                    $query->select('id', 'purchase_id', 'description', 'amount', 'expense_date', 'payment_method_id')
                        ->whereBetween('expense_date', [$fechaInicio, $fechaFin])
                        // ->whereBetween('expense_date', [["2025-07-01", "2025-07-31"]])
                        ->with(['paymentMethod']);
                }
            ])
            ->whereHas('expenses', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('expense_date', [[$fechaInicio, $fechaFin]]);
                //$query->whereBetween('expense_date', [["2025-07-01", "2025-07-31"]]);
            })
            ->where('user_id', 1)
            ->orderBy('id', 'desc')
            ->get();


        //        transformamos la colección para poner "method" directo
        $list->transform(function ($purchase) {
            $purchase->expenses->transform(function ($expense) {
                $expense->payment_method = $expense->paymentMethod->name ? $expense->paymentMethod->name : null;
                unset($expense->paymentMethod);
                unset($expense->payment_method_id);
                return $expense;
            });
            return $purchase;
        });

        $grouped = $list->groupBy('financing_method')->map(function ($items) {
            return [
                'total_count' => $items->count(),
                'total_sum' => $items->sum('total_price'),
                // 'purchases' => $items->values(),
            ];
        });

        return response()->json([
            'data' => $list,
            'grouped' => $grouped,
            'current_date' => $currentDate,
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
                'total_price'        => 'required|numeric',
                'user_id'            => 'required|numeric',
                'category_id'        => 'required|numeric',
                'financing_type_id'  => 'nullable|numeric',
                'financing_method'   => 'required|string',
                'payment_method_id'  => 'required|numeric',
                'comments'           => 'nullable|string',
                'purchase_date'      => 'required|date'
            ]);


            $purchase = Purchase::create([
                'product_name' => $request->input('product_name'),
                'total_price' => $request->input('total_price'),
                'user_id' => $request->input('user_id'),
                'category_id' => $request->input('category_id'),
                'financing_type_id' => $request->input('financing_type_id'),
                'financing_method' => $request->input('financing_method'),
                'payment_method_id' => $request->input('payment_method_id'),
                'comments' => $request->input('comments'),
                'purchase_date' => $request->input('purchase_date'),
            ]);


            Expense::create([
                'user_id' => $purchase->user_id,
                'amount' => $purchase->total_price,
                'expense_date' => $purchase->purchase_date,
                'description' => $purchase->product_name,
                'purchase_id' => $purchase->id,
                'payment_method_id' => $purchase->payment_method_id,
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

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase) {}

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {

            $validate = $request->validate([
                'product_name'       => 'required|string',
                'total_price'        => 'required|numeric',
                'user_id'            => 'required|numeric',
                'category_id'        => 'required|numeric',
                'financing_type_id'  => 'nullable|numeric',
                'financing_method'   => 'required|string',
                'payment_method_id'  => 'required|numeric',
                'comments'           => 'nullable|string',
                'purchase_date'      => 'required|date'
            ]);

            // Buscar el expense con su purchase
            $expense = Expense::with('purchase')->find($id);

            //$expense = Expense::with('purchase.payment_method')->find($id);


            // Expense::create([
            //     'user_id' => $purchase->user_id,
            //     'amount' => $purchase->total_price,
            //     'expense_date' => $purchase->purchase_date,
            //     'description' => $purchase->product_name,
            //     'purchase_id' => $purchase->id,
            //     'payment_method_id' => $purchase->payment_method_id,
            // ]);

            // $purchase = Purchase::create([
            //     'product_name' => $request->input('product_name'),
            //     'total_price' => $request->input('total_price'),
            //     'user_id' => $request->input('user_id'),
            //     'category_id' => $request->input('category_id'),
            //     'financing_type_id' => $request->input('financing_type_id'),
            //     'financing_method' => $request->input('financing_method'),
            //     'payment_method_id' => $request->input('payment_method_id'),
            //     'comments' => $request->input('comments'),
            //     'purchase_date' => $request->input('purchase_date'),
            // ]);

            // Actualizar expense con datos del request
            $expense->update([
                'amount' => $validate['total_price'],
                'expense_date' => $validate['purchase_date'],
                'description' => $validate['product_name'],
                'payment_method_id' => $validate['payment_method_id'],
            ]);

            // Si quieres actualizar datos de la purchase asociada
            if ($expense->purchase) {
                $expense->purchase->update([
                    'product_name' => $validate['product_name'] ?? $expense->purchase->product_name,
                    'total_price' => $validate['total_price'] ?? $expense->purchase->total_price,
                    'category_id' => $validate['category_id'] ?? $expense->purchase->category_id,
                    'financing_type_id' => $validate['financing_type_id'] ?? $expense->purchase->financing_type_id,
                    'financing_method' => $validate['financing_method'] ?? $expense->purchase->financing_method,
                    'payment_method_id' => $validate['payment_method_id'] ?? $expense->purchase->payment_method_id,
                    'comments' => $validate['comments'] ?? $expense->purchase->comments,
                    'purchase_date' => $validate['purchase_date'] ?? $expense->purchase->purchase_date,
                ]);
            }

            $expense->refresh(); // mismo que fresh()
            $expense->load('purchase.payment_method', 'purchase.category');

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
            $expense = Expense::with('purchase')->find($expenseId);

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
