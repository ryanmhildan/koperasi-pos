<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <!-- Total Anggota -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-500">Total Anggota</h3>
                    <p class="mt-1 text-3xl font-semibold">{{ number_format($total_anggota) }}</p>
                </div>
            </div>

            <!-- Total Simpanan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-500">Total Simpanan</h3>
                    <p class="mt-1 text-3xl font-semibold">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Total Pinjaman Aktif -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-500">Pinjaman Aktif</h3>
                    <p class="mt-1 text-3xl font-semibold">Rp {{ number_format($total_pinjaman_aktif, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Penjualan Hari Ini -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-500">Penjualan Hari Ini</h3>
                    <p class="mt-1 text-3xl font-semibold">Rp {{ number_format($penjualan_hari_ini, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Produk Stok Menipis -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-red-600">
                    <h3 class="text-lg font-medium text-red-500">Produk Stok Menipis</h3>
                    <p class="mt-1 text-3xl font-semibold">{{ number_format($produk_stok_menipis) }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Transaksi Terakhir</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($recent_transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->cashier->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->transaction_date->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada transaksi terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
