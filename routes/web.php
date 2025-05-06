<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('install', [ShopifyController::class, 'install'])->name('install');
Route::get('auth/callback', [ShopifyController::class, 'callback'])->name('auth.callback');
Route::get('subscribe-plan/{shop}', [ShopifyController::class, 'showSubscribeForm'])->name('subscribe');
Route::post('subscribe-plan', [ShopifyController::class, 'subscribePlan'])->name('subscribe.plan');

// Route::middleware('auth.shop')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/store', [ShopifyController::class, 'getShopData'])->name('store');
// });