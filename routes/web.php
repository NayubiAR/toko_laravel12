<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Dashboard\DashboardController;

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
    | INVENTORY ROUTES (Fase 2 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('inventory')->name('inventory.')->group(function () {
    //     Route::resource('products', ProductController::class);
    //     Route::resource('categories', CategoryController::class);
    // });

    /*
    |----------------------------------------------------------------------
    | POS ROUTES (Fase 3 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('pos')->name('pos.')->group(function () {
    //     Route::get('/', [PosController::class, 'index'])->name('index');
    // });

    /*
    |----------------------------------------------------------------------
    | SALES ROUTES (Fase 4 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('sales')->name('sales.')->group(function () {
    //     Route::get('/', [SaleController::class, 'index'])->name('index');
    // });

    /*
    |----------------------------------------------------------------------
    | PURCHASING ROUTES (Fase 6 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('purchasing')->name('purchasing.')->group(function () {
    //     Route::resource('suppliers', SupplierController::class);
    //     Route::resource('purchase-orders', PurchaseOrderController::class);
    // });

    /*
    |----------------------------------------------------------------------
    | CUSTOMER ROUTES (Fase 7 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('customers')->name('customers.')->group(function () {
    //     Route::resource('/', CustomerController::class);
    // });

    /*
    |----------------------------------------------------------------------
    | FINANCE ROUTES (Fase 5 - akan diisi nanti)
    |----------------------------------------------------------------------
    */
    // Route::prefix('finance')->name('finance.')->group(function () {
    //     Route::resource('cash-flow', CashFlowController::class);
    //     Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    //     Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    // });

    /*
    |----------------------------------------------------------------------
    | SETTINGS ROUTES (Admin & Owner only)
    |----------------------------------------------------------------------
    */
    // Route::middleware('role:admin|owner')->prefix('settings')->name('settings.')->group(function () {
    //     Route::get('/', [SettingController::class, 'index'])->name('index');
    //     Route::resource('users', UserController::class);
    //     Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
    // });
});