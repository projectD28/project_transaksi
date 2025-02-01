<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/penjualan', [TransactionController::class, 'Penjualan']);
Route::get('/komisi', [TransactionController::class, 'KomisiPenjualan']);
Route::get('/pembayaran', [PaymentController::class, 'Pembayaran']);
Route::post('/buat_pembayaran', [PaymentController::class, 'BuatPembayaran']);
