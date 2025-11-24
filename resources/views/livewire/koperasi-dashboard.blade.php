<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Dashboard Koperasi</h2>

                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Total Saldo Koperasi -->
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow-sm">
                        <p class="font-bold text-lg">Total Saldo Koperasi (Utama)</p>
                        <p class="text-3xl mt-1">Rp {{ number_format($totalKoperasiBalance, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Deposit Form --}}
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-bold mb-4 text-green-700">Setor Dana (Deposit)</h3>
                        <form wire:submit.prevent="confirmDeposit">
                            <div class="mb-4">
                                <label for="deposit_amount" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input wire:model="depositAmount" type="number" id="deposit_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('depositAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="deposit_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <input wire:model="depositDescription" type="text" id="deposit_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('depositDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700">
                                Simpan Deposit
                            </x-primary-button>
                        </form>
                    </div>

                    {{-- Withdraw Form --}}
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-bold mb-4 text-red-700">Tarik Dana (Withdraw)</h3>
                        <form wire:submit.prevent="confirmWithdraw">
                            <div class="mb-4">
                                <label for="withdraw_amount" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input wire:model="withdrawAmount" type="number" id="withdraw_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('withdrawAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="withdraw_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <input wire:model="withdrawDescription" type="text" id="withdraw_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('withdrawDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <x-danger-button type="submit">
                                Simpan Penarikan
                            </x-danger-button>
                        </form>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Riwayat Transaksi Koperasi</h3>
                    <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dilakukan Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $transaction->meta['description'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->meta['action_by_user_name'] ?? 'Sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada riwayat transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>

                <livewire:koperasi-transaction-confirmation-modal />
            </div>
        </div>
    </div>
</div>
