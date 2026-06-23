<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Admin ──────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|waiter|cashier|kitchen_staff'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Menu Management
    Route::resource('menu-sections', \App\Http\Controllers\Admin\MenuSectionController::class);
    Route::resource('menu-categories', \App\Http\Controllers\Admin\MenuCategoryController::class);
    Route::resource('menu-subcategories', \App\Http\Controllers\Admin\MenuSubcategoryController::class);
    Route::resource('menu-items', \App\Http\Controllers\Admin\MenuItemController::class);
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->except(['show']);
    
    // Tables Management
    Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);

    // Offers Management
    Route::resource('offers', \App\Http\Controllers\Admin\OfferController::class);
    Route::patch('offers/{offer}/toggle-status', [\App\Http\Controllers\Admin\OfferController::class, 'toggleStatus'])->name('offers.toggle-status');

    // Reservations Management
    Route::resource('reservations', \App\Http\Controllers\Admin\ReservationController::class);
    Route::patch('reservations/{reservation}/status', [\App\Http\Controllers\Admin\ReservationController::class, 'updateStatus'])->name('reservations.update-status');

    // Orders Management
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    Route::patch('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
});
