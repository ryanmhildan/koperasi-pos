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
        $this->adminMenu = [
            ['label' => 'Dashboard', 'description' => 'Halaman utama admin.', 'route' => 'admin.dashboard', 'permission' => 'view reports'],
            ['label' => 'Histori Shift', 'description' => 'Lihat riwayat shift kasir.', 'route' => 'report.shifts', 'permission' => 'view reports'],
            ['label' => 'Pengguna', 'description' => 'Kelola pengguna.', 'route' => 'admin.users', 'permission' => 'view users'],
            ['label' => 'Role', 'description' => 'Kelola role & izin.', 'route' => 'admin.roles', 'permission' => 'edit users'],
            ['label' => 'Harga', 'description' => 'Kelola harga jual.', 'route' => 'admin.pricing', 'permission' => 'edit products'],
            ['label' => 'Lokasi', 'description' => 'Kelola lokasi/toko.', 'route' => 'admin.locations', 'permission' => 'view locations'],
            ['label' => 'Kategori', 'description' => 'Kelola kategori produk.', 'route' => 'admin.categories', 'permission' => 'view categories'],
            ['label' => 'Unit', 'description' => 'Kelola satuan produk.', 'route' => 'admin.units', 'permission' => 'view units'],
            ['label' => 'Kartu Kredit', 'description' => 'Kelola kartu kredit anggota.', 'route' => 'admin.credit-cards', 'permission' => 'edit users'],
            ['label' => 'Dana Operasional', 'description' => 'Kelola dana operasional anggota.', 'route' => 'admin.operasional', 'permission' => 'edit users'],
        ];

        $this->kasirMenu = [
            ['label' => 'Buka Kasir (POS)', 'description' => 'Mulai sesi penjualan baru.', 'route' => 'pos.kasir', 'permission' => 'access pos', 'featured' => true],
            ['label' => 'Pengelolaan Stok', 'description' => 'Lihat dan kelola stok.', 'route' => 'pos.stock', 'permission' => 'view stock'],
            ['label' => 'Histori Stok', 'description' => 'Lihat riwayat pergerakan stok.', 'route' => 'pos.stock-movements', 'permission' => 'view stock'],
            ['label' => 'Pengelolaan Produk', 'description' => 'Lihat dan kelola produk.', 'route' => 'pos.products', 'permission' => 'view products'],
            ['label' => 'Penerimaan Barang', 'description' => 'Catat penerimaan barang baru.', 'route' => 'pos.grn.index', 'permission' => 'view grn'],
            ['label' => 'Riwayat Transaksi', 'description' => 'Lihat semua riwayat transaksi POS.', 'route' => 'report.transactions', 'permission' => 'access pos'],
        ];

        $this->koperasiMenu = [
            ['label' => 'Simpanan', 'description' => 'Kelola simpanan anggota.', 'route' => 'koperasi.simpanan', 'permission' => 'view simpanan'],
            ['label' => 'Pinjaman', 'description' => 'Kelola pinjaman anggota.', 'route' => 'koperasi.pinjaman', 'permission' => 'view pinjaman'],
            ['label' => 'Angsuran', 'description' => 'Kelola angsuran pinjaman.', 'route' => 'koperasi.angsuran', 'permission' => 'view angsuran'],
            ['label' => 'Cash Out', 'description' => 'Kelola penarikan tunai.', 'route' => 'koperasi.cashout', 'permission' => 'view cashout'],
            ['label' => 'Kartu Kredit Saya', 'description' => 'Lihat detail kartu kredit Anda.', 'route' => 'my-credit-card', 'permission' => 'view own credit card'],
            ['label' => 'Riwayat Transaksi Kartu Kredit', 'description' => 'Lihat riwayat transaksi kartu kredit Anda.', 'route' => 'my-credit-card.history', 'permission' => 'view own credit card'],
        ];

        $this->anggotaMenu = [
            ['label' => 'Simpanan Saya', 'description' => 'Lihat dan tambah simpanan.', 'route' => 'me.simpanan', 'permission' => 'isAnggota'],
            ['label' => 'Pinjaman Saya', 'description' => 'Lihat dan ajukan pinjaman.', 'route' => 'me.pinjaman', 'permission' => 'isAnggota'],
            ['label' => 'Angsuran Saya', 'description' => 'Lihat dan bayar angsuran.', 'route' => 'me.angsuran', 'permission' => 'isAnggota'],
            ['label' => 'Cash Out Saya', 'description' => 'Lihat dan ajukan penarikan.', 'route' => 'me.cashout', 'permission' => 'isAnggota'],
            ['label' => 'Histori Transaksi', 'description' => 'Lihat semua riwayat transaksi.', 'route' => 'me.history', 'permission' => 'isAnggota'],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
