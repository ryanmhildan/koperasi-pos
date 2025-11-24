<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Ringkasan Akun Saya</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                <h3 class="text-lg font-semibold">Total Saldo Simpanan</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($totalSimpanan, 2, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <h3 class="text-lg font-semibold">Total Pinjaman Aktif</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($totalPinjaman, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
