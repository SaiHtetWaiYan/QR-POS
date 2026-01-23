<?php

use App\Http\Controllers\CouponCampaignController;
use App\Http\Controllers\CouponCodeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Customer Routes
Route::prefix('t/{table}')->name('customer.')->middleware(\App\Http\Middleware\CustomerSessionExpired::class)->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::post('/cart', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CustomerController::class, 'viewCart'])->name('cart.view');
    Route::patch('/cart/{lineId}', [CustomerController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/{lineId}', [CustomerController::class, 'removeItem'])->name('cart.remove');
    Route::post('/coupon/check', [CustomerController::class, 'checkCoupon'])->name('coupon.check');
    Route::post('/order', [CustomerController::class, 'placeOrder'])->name('order.place');
    Route::get('/status', [CustomerController::class, 'status'])->name('status');
    Route::post('/order/{order}/bill', [CustomerController::class, 'requestBill'])->name('order.bill');
});

// POS / Admin Routes
Route::middleware(['auth', 'verified'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/history', [PosController::class, 'history'])->name('history');
    Route::get('/reports', [PosController::class, 'reports'])->name('reports');
    Route::get('/reports/export/{range}', [PosController::class, 'exportReports'])->name('reports.export');
    Route::get('/coupons', [CouponCampaignController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [CouponCampaignController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [CouponCampaignController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{campaign}', [CouponCampaignController::class, 'show'])->name('coupons.show');
    Route::get('/coupons/{campaign}/edit', [CouponCampaignController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{campaign}', [CouponCampaignController::class, 'update'])->name('coupons.update');
    Route::patch('/coupons/{campaign}/toggle', [CouponCampaignController::class, 'toggle'])->name('coupons.toggle');
    Route::post('/coupons/{campaign}/generate', [CouponCampaignController::class, 'generate'])->name('coupons.generate');
    Route::delete('/coupons/{campaign}', [CouponCampaignController::class, 'destroy'])->name('coupons.destroy');
    Route::patch('/coupon-codes/{couponCode}/disable', [CouponCodeController::class, 'disable'])->name('coupon-codes.disable');
    Route::patch('/coupon-codes/{couponCode}/enable', [CouponCodeController::class, 'enable'])->name('coupon-codes.enable');
    Route::delete('/coupon-codes/{couponCode}', [CouponCodeController::class, 'destroyCode'])->name('coupon-codes.destroy');
    Route::get('/orders/{order}', [PosController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [PosController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}/print', [PosController::class, 'print'])->name('orders.print');
    Route::get('/orders/{order}/card', [PosController::class, 'orderCard'])->name('orders.card');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Menu
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu/categories', [MenuController::class, 'storeCategory'])->name('menu.categories.store');
    Route::delete('/menu/categories/{category}', [MenuController::class, 'destroyCategory'])->name('menu.categories.destroy');
    Route::get('/menu/items/create', [MenuController::class, 'createItem'])->name('menu.items.create');
    Route::post('/menu/items', [MenuController::class, 'storeItem'])->name('menu.items.store');
    Route::get('/menu/items/{menuItem}/edit', [MenuController::class, 'editItem'])->name('menu.items.edit');
    Route::put('/menu/items/{menuItem}', [MenuController::class, 'updateItem'])->name('menu.items.update');
    Route::patch('/menu/items/{menuItem}/toggle', [MenuController::class, 'toggleItem'])->name('menu.items.toggle');
    Route::delete('/menu/items/{menuItem}', [MenuController::class, 'destroyItem'])->name('menu.items.destroy');

    // Tables
    Route::resource('tables', TableController::class);
    Route::get('/tables/{table}/qr', [TableController::class, 'qr'])->name('tables.qr');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
