<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Purchasing\SupplierController;

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/',      [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login',[LoginController::class, 'login'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\EnsureActiveUser::class])->group(function () {

    // ── Logout ──
    Route::post('/logout', LogoutController::class)->name('logout');

    // ── Dashboard ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Profile ──
    Route::get('/profile',            [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',   [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |----------------------------------------------------------------------
    | INVENTORY ROUTES (Fase 2)
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:manage-products|view-products')
        ->prefix('inventory')
        ->name('inventory.')
        ->group(function () {

        // Produk
        Route::resource('products', ProductController::class);

        // Kategori (tanpa show & edit, pakai modal)
        Route::resource('categories', CategoryController::class)->except(['show', 'edit', 'create']);
    });

    /*
    |----------------------------------------------------------------------
    | PURCHASING ROUTES (Fase 2 - Supplier)
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:manage-suppliers')
        ->prefix('purchasing')
        ->name('purchasing.')
        ->group(function () {

        Route::resource('suppliers', SupplierController::class);
    });

    /*
    |----------------------------------------------------------------------
    | POS ROUTES (Fase 3 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('pos')->name('pos.')->group(function () { ... });

    /*
    |----------------------------------------------------------------------
    | SALES ROUTES (Fase 4 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('sales')->name('sales.')->group(function () { ... });

    /*
    |----------------------------------------------------------------------
    | FINANCE ROUTES (Fase 5 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('finance')->name('finance.')->group(function () { ... });

    /*
    |----------------------------------------------------------------------
    | CUSTOMER ROUTES (Fase 7 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('customers')->name('customers.')->group(function () { ... });

    /*
    |----------------------------------------------------------------------
    | SETTINGS ROUTES (Admin & Owner only)
    |----------------------------------------------------------------------
    */
    // Route::middleware('role:admin|owner')->prefix('settings')->name('settings.')->group(function () { ... });
});