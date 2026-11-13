<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS & Penjualan (Akses: Kasir & Admin Gudang)
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/', [PosController::class, 'store'])->name('store');
        Route::get('/history', [PosController::class, 'history'])->name('history');
        Route::get('/receipt/{sale}', [PosController::class, 'receipt'])->name('receipt');
    });

    // Produk (Daftar produk & detail bisa dilihat oleh Admin Gudang dan Kasir)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Laporan yang dapat diakses oleh Kasir & Admin (Laporan Stok & Laporan Penjualan)
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');

    // ==========================================
    // RUTE KHUSUS ADMIN GUDANG
    // ==========================================
    Route::middleware(['role:admin_gudang'])->group(function () {
        // Kelola Produk (Tambah, Edit, Hapus)
        Route::get('/products-create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Master Kategori
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);

        // Master Supplier
        Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);

        // Transaksi Stok Masuk
        Route::prefix('stock-in')->name('stock-in.')->group(function () {
            Route::get('/', [StockInController::class, 'index'])->name('index');
            Route::get('/create', [StockInController::class, 'create'])->name('create');
            Route::post('/', [StockInController::class, 'store'])->name('store');
            Route::get('/{stockIn}', [StockInController::class, 'show'])->name('show');
            Route::get('/{stockIn}/print', [StockInController::class, 'print'])->name('print');
        });

        // Transaksi Stok Keluar Gudang
        Route::prefix('stock-out')->name('stock-out.')->group(function () {
            Route::get('/', [StockOutController::class, 'index'])->name('index');
            Route::get('/create', [StockOutController::class, 'create'])->name('create');
            Route::post('/', [StockOutController::class, 'store'])->name('store');
            Route::get('/{stockOut}', [StockOutController::class, 'show'])->name('show');
            Route::get('/{stockOut}/print', [StockOutController::class, 'print'])->name('print');
        });

        // Menu Hub Laporan & Laporan Khusus Admin Gudang
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/stock-in', [ReportController::class, 'stockIn'])->name('stock-in');
            Route::get('/stock-out', [ReportController::class, 'stockOut'])->name('stock-out');
        });

        // Manajemen Pengguna
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
    });
});
