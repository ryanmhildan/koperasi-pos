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
        $anggota = User::role('Anggota')->get();
        $totalSimpanan = 0;
        foreach ($anggota as $member) {
            $simpananWallet = $member->getWallet('simpanan');
            if ($simpananWallet) {
                $totalSimpanan += $simpananWallet->balance;
            }
        }

        $data = [
            'total_anggota' => $anggota->count(),
            'total_simpanan' => $totalSimpanan,
            'total_pinjaman_aktif' => Pinjaman::where('status', 'active')->sum('remaining_balance'),
            'penjualan_hari_ini' => SalesTransaction::whereDate('transaction_date', today())
                ->where('status', 'completed')->sum('total_amount'),
            'produk_stok_menipis' => Product::whereHas('stocks', function($query) {
                $query->whereRaw('current_stock <= minimum_stock');
            })->count(),
        ];

        return view('livewire.admin-dashboard', $data)
            ->layout('layouts.app', [
                'header' => 'Admin Dashboard',
            ]);
    }
}
