<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\SalesTransaction;
use App\Models\Product;

class AdminDashboard extends Component
{
    public function render()
    {
        $data = [
            'total_anggota' => User::role('Anggota')->count(),
            'total_simpanan' => Simpanan::sum('amount'),
            'total_pinjaman_aktif' => Pinjaman::where('status', 'active')->sum('remaining_balance'),
            'penjualan_hari_ini' => SalesTransaction::whereDate('transaction_date', today())
                ->where('status', 'completed')->sum('total_amount'),
            'produk_stok_menipis' => Product::whereHas('stocks', function($query) {
                $query->whereRaw('current_stock <= minimum_stock');
            })->count(),
            'recent_transactions' => SalesTransaction::with(['customer', 'cashier', 'cashDrawer.location'])
                ->latest()->take(5)->get(),
        ];

        return view('livewire.admin-dashboard', $data)
            ->layout('layouts.app', [
                'header' => 'Admin Dashboard',
            ]);
    }
}
