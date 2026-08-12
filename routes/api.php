<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned the "api"
| middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::prefix('categories')->group(function () {
    Route::get('expense', [CategoryController::class, 'listExpense']);
    Route::get('income',  [CategoryController::class, 'listIncome']);
    Route::get('/getCategoryList', [CategoryController::class, 'todas']);

});

Route::prefix('expenses')->group(function () {
    Route::get('/',       [ExpenseController::class, 'list']);
    Route::post('/',      [ExpenseController::class, 'store']);
    Route::put('/{id}',   [ExpenseController::class, 'update']);
    Route::delete('/{id}',[ExpenseController::class, 'destroy']);
});

Route::prefix('incomes')->group(function () {
    Route::get('/',       [IncomeController::class, 'list']);
    Route::post('/',      [IncomeController::class, 'store']);
    Route::put('/{id}',   [IncomeController::class, 'update']);
    Route::delete('/{id}',[IncomeController::class, 'destroy']);
});

Route::get('payment-methods', [PaymentMethodController::class, 'list']);