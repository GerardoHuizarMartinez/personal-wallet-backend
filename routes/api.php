<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ColonyController;
use App\Http\Controllers\RoleController;
use App\Mappers\UserMapper;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/sanctum/csrf-cookie', function () {
    return response()->noContent(204)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return UserMapper::toArray($request->user()->load('role.permissions'));
    });

    Route::get('/me', [AuthController::class, 'me']);


    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard,view');


    Route::prefix('categories')->middleware('permission:categories,view')->group(function () {
        Route::get('expense', [CategoryController::class, 'listExpense']);
        Route::get('income',  [CategoryController::class, 'listIncome']);
        Route::get('/getCategoryList', [CategoryController::class, 'todas']);
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:categories,create');
        Route::put('/{id}', [CategoryController::class, 'update'])->middleware('permission:categories,edit');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware('permission:categories,delete');
    });

    Route::prefix('expenses')->middleware('permission:expenses,view')->group(function () {
        Route::get('/',       [ExpenseController::class, 'list']);
        Route::post('/',      [ExpenseController::class, 'store'])->middleware('permission:expenses,create');
        Route::put('/{id}',   [ExpenseController::class, 'update'])->middleware('permission:expenses,edit');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->middleware('permission:expenses,delete');
        Route::get('/download-expenses-report', [ExpenseController::class, 'downloadExcelWithExpensesByDate'])
            ->middleware('permission:reports,view');
        Route::get('/{id}',   [ExpenseController::class, 'show']);
    });

    Route::prefix('incomes')->middleware('permission:income,view')->group(function () {
        Route::get('/',       [IncomeController::class, 'list']);
        Route::post('/',      [IncomeController::class, 'store'])->middleware('permission:income,create');
        Route::put('/{id}',   [IncomeController::class, 'update'])->middleware('permission:income,edit');
        Route::delete('/{id}', [IncomeController::class, 'destroy'])->middleware('permission:income,delete');
        Route::get('/{id}',   [IncomeController::class, 'show']);
    });

    Route::get('payment-methods', [PaymentMethodController::class, 'list']);

    Route::prefix('users')->middleware('permission:users,view')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users,create');
        Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:users,edit');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users,delete');
        Route::post('/{id}/photo', [UserController::class, 'uploadPhoto'])->middleware('permission:users,edit');
        Route::delete('/{id}/photo', [UserController::class, 'deletePhoto'])->middleware('permission:users,edit');
    });

    Route::prefix('roles')->middleware('permission:roles,view')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles,create');
        Route::put('/{id}', [RoleController::class, 'update'])->middleware('permission:roles,edit');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles,delete');
    });

    Route::prefix('colonies')->group(function () {
        Route::get('/', [ColonyController::class, 'index']);
        Route::get('/{id}', [ColonyController::class, 'show']);
    });


    Route::post('/logout', [AuthController::class, 'logout']);
});
