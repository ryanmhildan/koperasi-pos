<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Riwayat Transaksi Wallet</h2>

        <div class="mb-4">
            <label for="walletType" class="block text-sm font-medium text-gray-700">Pilih Wallet</label>
            <select wire:model.live="walletType" id="walletType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="all">Semua</option>
                @foreach ($wallets as $wallet)
                    <option value="{{ $wallet->slug }}">{{ $wallet->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->meta['description'] ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Tidak ada transaksi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
