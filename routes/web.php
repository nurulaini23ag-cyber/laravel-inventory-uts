<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Inventory & Sales Management System
|--------------------------------------------------------------------------
*/

// 1. Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terproteksi Autentikasi (Harus Login)
Route::middleware('auth')->group(function () {

    // Halaman Awal / Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Rute Khusus User / Pelanggan (Pemesanan & Status)
    Route::get('/catalog', [OrderController::class, 'catalog'])->name('orders.catalog');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Rute Operasional Khusus Staf & Admin (Staff Kasir & Owner)
    // Pengguna biasa (role: user) DILARANG mengakses data sensitif toko di bawah ini
    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);

        // Modul Pengadaan Stok (Purchases / PO)
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        // Modul Penjualan Kasir (POS)
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

        // Konfirmasi & Penolakan Pesanan Pelanggan oleh Kasir
        Route::post('/sales/{sale}/confirm', [SaleController::class, 'confirm'])->name('sales.confirm');
        Route::post('/sales/{sale}/reject', [SaleController::class, 'reject'])->name('sales.reject');

        // Audit Trail Mutasi Stok Toko
        Route::get('/stock/history', [StockMovementController::class, 'history'])->name('stock.history');
        Route::get('/stock/in', [StockMovementController::class, 'createIn'])->name('stock.in');
        Route::post('/stock/in', [StockMovementController::class, 'storeIn'])->name('stock.in.store');
        Route::get('/stock/out', [StockMovementController::class, 'createOut'])->name('stock.out');
        Route::post('/stock/out', [StockMovementController::class, 'storeOut'])->name('stock.out.store');
    });

    // Rute Khusus Hak Akses Admin / Owner Saja
    Route::middleware('role:admin')->group(function () {
        // User & Role Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Pembatalan Transaksi dengan Reversal Stok
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    });
});
