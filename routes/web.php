<?php



use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Inventory & Sales Management System (UTS Integrated Project)
|--------------------------------------------------------------------------
*/

//  1. Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terproteksi Autentikasi (Harus Login)
Route::middleware('auth')->group(function () {

    //  Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    //  2. Master Data Products (Resource Route)
    Route::resource('products', ProductController::class);

    //  3. Master Data Categories (Resource Route)
    Route::resource('categories', CategoryController::class);

    //  4. Master Data Suppliers (Resource Route)
    Route::resource('suppliers', SupplierController::class);

    //  5. Purchases / Stock-In Module
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    //  6. Sales / Stock-Out Module
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    //  Audit Trail Mutasi Stok Terpadu
    Route::get('/stock/history', [StockMovementController::class, 'history'])->name('stock.history');
    Route::get('/stock/in', [StockMovementController::class, 'createIn'])->name('stock.in');
    Route::post('/stock/in', [StockMovementController::class, 'storeIn'])->name('stock.in.store');
    Route::get('/stock/out', [StockMovementController::class, 'createOut'])->name('stock.out');
    Route::post('/stock/out', [StockMovementController::class, 'storeOut'])->name('stock.out.store');

    //  Role-Based Access: Khusus Hak Akses Admin / Owner
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
