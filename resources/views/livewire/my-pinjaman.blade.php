<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Pinjaman Saya</h2>

        <!-- Form to Request Pinjaman -->
        <form wire:submit.prevent="requestPinjaman" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="loan_amount" class="block text-sm font-medium text-gray-700">Jumlah Pinjaman</label>
                    <input wire:model="loan_amount" type="number" id="loan_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('loan_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="tenor_months" class="block text-sm font-medium text-gray-700">Tenor (Bulan)</label>
                    <input wire:model="tenor_months" type="number" id="tenor_months" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('tenor_months') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="loan_type" class="block text-sm font-medium text-gray-700">Jenis Pinjaman</label>
                    <select wire:model="loan_type" id="loan_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="biasa">Biasa</option>
                        <option value="khusus">Khusus</option>
                        <option value="darurat">Darurat</option>
                    </select>
                    @error('loan_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="loan_purpose" class="block text-sm font-medium text-gray-700">Tujuan Pinjaman</label>
                    <input wire:model="loan_purpose" type="text" id="loan_purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('loan_purpose') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Ajukan Pinjaman
                </button>
            </div>
        </form>

        <!-- Pinjaman Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pinjaman as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->loan_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->loan_amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->tenor_months }} bulan</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($item->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('active') bg-green-100 text-green-800 @break
                                        @case('closed') bg-blue-100 text-blue-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ $item->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->remaining_balance, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Belum ada data pinjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pinjaman->links() }}
        </div>
    </div>
</div>