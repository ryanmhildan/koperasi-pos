<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\SimpananManagement;
use App\Livewire\PinjamanManagement;
use App\Livewire\AngsuranManagement;
use App\Livewire\PosKasir;
use App\Livewire\ProductManagement;
use App\Livewire\StockManagement;
use App\Livewire\UserManagement;
use App\Livewire\RoleManagement;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Koperasi Routes
    Route::prefix('koperasi')->name('koperasi.')->group(function () {
        Route::get('/simpanan', SimpananManagement::class)->name('simpanan');
        Route::get('/pinjaman', PinjamanManagement::class)->name('pinjaman');
        Route::get('/angsuran', AngsuranManagement::class)->name('angsuran');
        // Assuming CashOutTransaction management is handled within a different component or needs a new one.
        // For now, let's create a placeholder view for cashout.
        Route::get('/cashout', function() { return view('livewire.coming-soon'); })->name('cashout');
    });

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/kasir', PosKasir::class)->name('kasir');
        Route::get('/products', ProductManagement::class)->name('products');
        Route::get('/stock', StockManagement::class)->name('stock');
        // Assuming GRN management needs a new component.
        Route::get('/grn', function() { return view('livewire.coming-soon'); })->name('grn');
    });

    // Admin only routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/roles', RoleManagement::class)->name('roles');
    });
    
    // Profile route from Laravel Breeze
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

});

require __DIR__.'/auth.php';
