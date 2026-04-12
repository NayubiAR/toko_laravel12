<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\StockMovementController;
use App\Http\Controllers\Purchasing\SupplierController;
use App\Http\Controllers\Sales\PosController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\ReceiptController;
use App\Http\Controllers\Finance\CashFlowController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\LoyaltyController;

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

    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',            [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',   [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |----------------------------------------------------------------------
    | POS ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:create-sale')
        ->prefix('pos')
        ->name('pos.')
        ->group(function () {
        Route::get('/',           [PosController::class, 'index'])->name('index');
        Route::post('/checkout',  [PosController::class, 'checkout'])->name('checkout');
        Route::get('/products',   [PosController::class, 'searchProducts'])->name('products.search');
    });

    /*
    |----------------------------------------------------------------------
    | SALES & RECEIPT ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:view-sales|create-sale')
        ->prefix('sales')
        ->name('sales.')
        ->group(function () {
        Route::get('/',                    [SaleController::class, 'index'])->name('index');
        Route::get('/{sale}',              [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/receipt',      [ReceiptController::class, 'thermal'])->name('receipt.thermal');
        Route::get('/{sale}/invoice',      [ReceiptController::class, 'a4'])->name('receipt.a4');
        Route::get('/{sale}/download/{format?}', [ReceiptController::class, 'download'])->name('receipt.download');
    });

    /*
    |----------------------------------------------------------------------
    | INVENTORY ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:manage-products|view-products')
        ->prefix('inventory')
        ->name('inventory.')
        ->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class)->except(['show', 'edit', 'create']);

        Route::get('/stock-movements',         [StockMovementController::class, 'index'])->name('stock-movements.index');
        Route::get('/stock-movements/create',  [StockMovementController::class, 'create'])->name('stock-movements.create');
        Route::post('/stock-movements',        [StockMovementController::class, 'store'])->name('stock-movements.store');
    });

    /*
    |----------------------------------------------------------------------
    | PURCHASING ROUTES
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
    | FINANCE ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:view-reports|view-cash-flow|view-profit-loss')
        ->prefix('finance')
        ->name('finance.')
        ->group(function () {
        Route::get('/cash-flow',          [CashFlowController::class, 'index'])->name('cash-flow.index');
        Route::get('/cash-flow/create',   [CashFlowController::class, 'create'])->name('cash-flow.create');
        Route::post('/cash-flow',         [CashFlowController::class, 'store'])->name('cash-flow.store');
        Route::delete('/cash-flow/{cashFlow}', [CashFlowController::class, 'destroy'])->name('cash-flow.destroy');

        Route::get('/reports/sales',       [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    });

    /*
    |----------------------------------------------------------------------
    | CUSTOMER & LOYALTY ROUTES (Fase 7)
    |----------------------------------------------------------------------
    */
    Route::middleware('permission:manage-customers|view-customers')
        ->prefix('customers')
        ->name('customers.')
        ->group(function () {
        Route::get('/',              [CustomerController::class, 'index'])->name('index');
        Route::get('/create',        [CustomerController::class, 'create'])->name('create');
        Route::post('/',             [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}',    [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}',    [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

        // Loyalty - adjust poin
        Route::post('/{customer}/adjust-points', [LoyaltyController::class, 'adjustPoints'])->name('adjust-points');
    });
});