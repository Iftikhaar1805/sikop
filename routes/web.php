<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::middleware('role:super_admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    });
    Route::resource('transactions', TransactionController::class)->only(['index', 'store']);
    Route::get('/transaksi/{transaction}/struk', [TransactionController::class, 'receipt'])
        ->name('transactions.receipt');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/anggota', [MemberController::class, 'index'])->name('members.index');
        Route::post('/anggota', [MemberController::class, 'store'])->name('members.store');
        Route::get('/anggota/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::put('/anggota/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::post('/anggota/{member}/simpanan', [MemberController::class, 'storeSavings'])->name('members.savings.store');
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
    });
});
