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
use App\Livewire\Report\TransactionHistory;
use App\Livewire\Report\ShiftHistory;
use App\Livewire\CashOutManagement;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Member Routes
    Route::middleware(['auth'])->prefix('me')->name('me.')->group(function () {
        Route::get('/simpanan', \App\Livewire\MySimpanan::class)->name('simpanan')->middleware('permission:view own simpanan');
        Route::get('/pinjaman', \App\Livewire\MyPinjaman::class)->name('pinjaman')->middleware('permission:view own pinjaman');
        Route::get('/angsuran', \App\Livewire\MyAngsuran::class)->name('angsuran')->middleware('permission:view own angsuran');
        Route::get('/cashout', \App\Livewire\MyCashOut::class)->name('cashout')->middleware('permission:view own cashout');
        Route::get('/wallet-history', \App\Livewire\MyWalletHistory::class)->name('wallet-history')->middleware('permission:isAnggota');
        Route::get('/summary', \App\Livewire\MySummary::class)->name('summary')->middleware('permission:isAnggota');
    });

    // Koperasi Routes
    Route::prefix('koperasi')->name('koperasi.')->group(function () {
        Route::get('/dashboard', \App\Livewire\KoperasiDashboard::class)->name('dashboard')->middleware('permission:view koperasi dashboard');
        Route::get('/financial-summary', \App\Livewire\UserFinancialSummary::class)->name('financial-summary')->middleware('permission:view koperasi dashboard');
        Route::get('/simpanan', SimpananManagement::class)->name('simpanan');
        Route::get('/pinjaman', PinjamanManagement::class)->name('pinjaman');
        Route::get('/angsuran/{pinjamanId?}', \App\Livewire\AdminAngsuranManagement::class)->name('angsuran');
        Route::get('/cashout', \App\Livewire\AdminCashOutManagement::class)->name('cashout');
    });

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/kasir', PosKasir::class)->name('kasir');
        Route::get('/products', ProductManagement::class)->name('products');
        Route::get('/stock', StockManagement::class)->name('stock');
        Route::get('/stock-movements', \App\Livewire\StockMovementManagement::class)->name('stock-movements');

        // GRN Routes
        Route::get('/grn', \App\Livewire\GrnList::class)->name('grn.index');
        Route::get('/grn/create', \App\Livewire\GrnCreate::class)->name('grn.create');
    });

    // Report Routes
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/transactions', TransactionHistory::class)->name('transactions');
        Route::get('/shifts', ShiftHistory::class)->name('shifts');
    });

    // Admin only routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\AdminDashboard::class)->name('dashboard');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/roles', RoleManagement::class)->name('roles');
        Route::get('/pricing', \App\Livewire\PriceManagement::class)->name('pricing');
        Route::get('/locations', \App\Livewire\LocationManagement::class)->name('locations');
        Route::get('/categories', \App\Livewire\CategoryManagement::class)->name('categories');
        Route::get('/units', \App\Livewire\UnitManagement::class)->name('units');
        Route::get('/top-up-simpanan', \App\Livewire\TopUpSimpanan::class)->name('top-up-simpanan');
    });
    
    // Profile route from Laravel Breeze
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

});

require __DIR__.'/auth.php';
