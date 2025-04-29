<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('install', [ShopifyController::class, 'install']);
Route::get('auth/callback', [ShopifyController::class, 'callback']);

Route::middleware('auth.shop')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/store', [ShopifyController::class, 'getShopData'])->name('store');
});