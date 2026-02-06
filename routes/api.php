<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymenMethodController;


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


Route::get('/getListPurchasesCurrent', [PurchaseController::class, 'list']);
Route::get('/getCategoryList', [CategoryController::class, 'list']);
Route::get('/getPaymentMethodList', [PaymenMethodController::class, 'list']);

Route::post('/store-purchase-expense', [PurchaseController::class, 'store']);
Route::delete('/delete/{expense}', [PurchaseController::class, 'destroy']);
Route::put('/update/{id}', [PurchaseController::class, 'update']);