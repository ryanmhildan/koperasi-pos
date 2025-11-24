<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $adminMenu = [];
    public $kasirMenu = [];
    public $koperasiMenu = [];
    public $anggotaMenu = [];
    public string $role = '';

    public function mount()
    {
        $this->role = auth()->user()->getRoleNames()->first() ?? '';
        $this->initializeMenus();
    }

    private function initializeMenus()
{
    // =======================
    // ADMIN MENU
    // =======================
    $this->adminMenu = [
        // DASHBOARD
        ['label' => 'Dashboard', 'description' => 'Halaman utama admin.', 'route' => 'admin.dashboard', 'permission' => 'view reports'],

        // MANAJEMEN PENGGUNA
        ['label' => 'Pengguna', 'description' => 'Kelola pengguna.', 'route' => 'admin.users', 'permission' => 'view users'],
        ['label' => 'Role', 'description' => 'Kelola role & izin.', 'route' => 'admin.roles', 'permission' => 'edit users'],
        ['label' => 'Histori Shift', 'description' => 'Lihat riwayat shift kasir.', 'route' => 'report.shifts', 'permission' => 'view reports'],

        // DATA MASTER
        ['label' => 'Lokasi', 'description' => 'Kelola lokasi/toko.', 'route' => 'admin.locations', 'permission' => 'view locations'],
        ['label' => 'Kategori', 'description' => 'Kelola kategori produk.', 'route' => 'admin.categories', 'permission' => 'view categories'],
        ['label' => 'Unit', 'description' => 'Kelola satuan produk.', 'route' => 'admin.units', 'permission' => 'view units'],
        ['label' => 'Harga', 'description' => 'Kelola harga jual.', 'route' => 'admin.pricing', 'permission' => 'edit products'],

        // KEANGGOTAAN KOPERASI
        ['label' => 'Top Up Simpanan', 'description' => 'Top up saldo simpanan anggota.', 'route' => 'admin.top-up-simpanan', 'permission' => 'edit users'],
    ];

    // =======================
    // KASIR MENU
    // =======================
    $this->kasirMenu = [
        // POS
        ['label' => 'Buka Kasir (POS)', 'description' => 'Mulai sesi penjualan baru.', 'route' => 'pos.kasir', 'permission' => 'access pos', 'featured' => true],
        ['label' => 'Riwayat Transaksi', 'description' => 'Lihat semua riwayat transaksi POS.', 'route' => 'report.transactions', 'permission' => 'access pos'],

        // MANAJEMEN STOK
        ['label' => 'Pengelolaan Stok', 'description' => 'Lihat dan kelola stok.', 'route' => 'pos.stock', 'permission' => 'view stock'],
        ['label' => 'Histori Stok', 'description' => 'Lihat riwayat pergerakan stok.', 'route' => 'pos.stock-movements', 'permission' => 'view stock'],
        ['label' => 'Pengelolaan Produk', 'description' => 'Lihat dan kelola produk.', 'route' => 'pos.products', 'permission' => 'view products'],
        ['label' => 'Penerimaan Barang', 'description' => 'Catat penerimaan barang baru.', 'route' => 'pos.grn.index', 'permission' => 'view grn'],
    ];

    // =======================
    // KOPERASI MENU
    // =======================
    $this->koperasiMenu = [
        ['label' => 'Dashboard', 'description' => 'Ringkasan keuangan koperasi.', 'route' => 'koperasi.dashboard', 'permission' => 'view koperasi dashboard'],
        ['label' => 'Ringkasan Keuangan', 'description' => 'Lihat ringkasan keuangan semua anggota.', 'route' => 'koperasi.financial-summary', 'permission' => 'view koperasi dashboard'],
        ['label' => 'Simpanan', 'description' => 'Kelola simpanan anggota.', 'route' => 'koperasi.simpanan', 'permission' => 'view simpanan'],
        ['label' => 'Pinjaman', 'description' => 'Kelola pinjaman anggota.', 'route' => 'koperasi.pinjaman', 'permission' => 'view pinjaman'],
        ['label' => 'Angsuran', 'description' => 'Kelola angsuran pinjaman.', 'route' => 'koperasi.angsuran', 'permission' => 'view angsuran'],
        ['label' => 'Cash Out', 'description' => 'Kelola penarikan tunai.', 'route' => 'koperasi.cashout', 'permission' => 'view cashout'],
    ];

    // =======================
    // ANGGOTA MENU
    // =======================
    $this->anggotaMenu = [
        ['label' => 'Ringkasan Akun', 'description' => 'Lihat ringkasan simpanan dan pinjaman.', 'route' => 'me.summary', 'permission' => 'isAnggota'],
        ['label' => 'Simpanan Saya', 'description' => 'Lihat dan tambah simpanan.', 'route' => 'me.simpanan', 'permission' => 'isAnggota'],
        ['label' => 'Pinjaman Saya', 'description' => 'Lihat dan ajukan pinjaman.', 'route' => 'me.pinjaman', 'permission' => 'isAnggota'],
        ['label' => 'Angsuran Saya', 'description' => 'Lihat dan bayar angsuran.', 'route' => 'me.angsuran', 'permission' => 'isAnggota'],
        ['label' => 'Cash Out Saya', 'description' => 'Lihat dan ajukan penarikan.', 'route' => 'me.cashout', 'permission' => 'isAnggota'],

        ['label' => 'Riwayat Transaksi Wallet', 'description' => 'Lihat semua riwayat transaksi wallet.', 'route' => 'me.wallet-history', 'permission' => 'isAnggota'],
    ];
}


    public function render()
    {
        return view('livewire.dashboard');
    }
}
