<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Pengajuan Cash Out</h2>

        <!-- Current Balance -->
        <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <p class="font-bold">Saldo Simpanan Anda Saat Ini:</p>
            <p class="text-2xl">Rp {{ number_format($current_balance, 2, ',', '.') }}</p>
        </div>

        <!-- Form to Request Cash Out -->
        <form wire:submit.prevent="requestCashOut" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input wire:model="amount" type="number" id="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input wire:model="notes" type="text" id="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Ajukan Cash Out
                    </button>
                </div>
            </div>
        </form>

        <!-- Cash Out History Table -->
        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold mb-2">Riwayat Pengajuan</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cashouts as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->notes }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($item->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Belum ada riwayat pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $cashouts->links() }}
        </div>
    </div>
</div>