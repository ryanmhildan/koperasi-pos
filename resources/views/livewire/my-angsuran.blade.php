<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Angsuran Saya</h2>

        <!-- Loan Selection and Payment Form -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label for="pinjaman_id" class="block text-sm font-medium text-gray-700">Pilih Pinjaman</label>
                    <select wire:model.live="pinjaman_id" id="pinjaman_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Pilih Pinjaman --</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->pinjaman_id }}">
                                Pinjaman Rp {{ number_format($loan->loan_amount, 0, ',', '.') }} - {{ $loan->loan_purpose }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($pinjaman_id)
                <div class="md:col-span-2">
                    <form wire:submit.prevent="payAngsuran">
                        <div class="flex items-end space-x-4">
                            <div class="flex-grow">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Pembayaran</label>
                                <input wire:model="amount" type="number" id="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 sm:text-sm" readonly>
                                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Bayar Angsuran
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Angsuran History Table -->
        @if($pinjaman_id)
        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold mb-2">Riwayat Angsuran</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Jatuh Tempo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($installments as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->paid_date ? \Carbon\Carbon::parse($item->paid_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->denda, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $item->status_badge_color }}-100 text-{{ $item->status_badge_color }}-800">
                                    {{ $item->status_text }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Belum ada riwayat angsuran untuk pinjaman ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>