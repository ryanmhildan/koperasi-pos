<?php

use App\Http\Controllers\{DashboardController, KoperasiController, PosController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Koperasi Routes
    Route::prefix('koperasi')->name('koperasi.')->group(function () {
        Route::get('/simpanan', [KoperasiController::class, 'simpanan'])->name('simpanan');
        Route::get('/pinjaman', [KoperasiController::class, 'pinjaman'])->name('pinjaman');
        Route::get('/angsuran', [KoperasiController::class, 'angsuran'])->name('angsuran');
        Route::get('/cashout', [KoperasiController::class, 'cashout'])->name('cashout');
    });
    
    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/kasir', [PosController::class, 'kasir'])->name('kasir');
        Route::get('/products', [PosController::class, 'products'])->name('products');
        Route::get('/stock', [PosController::class, 'stock'])->name('stock');
        Route::get('/grn', [PosController::class, 'grn'])->name('grn');
    });
    
    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', function() { return view('admin.users'); })->name('users');
        Route::get('/roles', function() { return view('admin.roles'); })->name('roles');
    });
});

require __DIR__.'/auth.php';
