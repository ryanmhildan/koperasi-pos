<div class="p-6 bg-white border-b border-gray-200">
    <div class="flex flex-wrap items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Penarikan Tunai (Cash Out)</h2>
        <button wire:click="requestCashOut()" class="px-4 py-2 mt-2 font-bold text-white bg-green-500 rounded-full sm:mt-0 hover:bg-green-700">
            Ajukan Penarikan
        </button>
    </div>

    <!-- Cash Out Limit Info -->
    @if($creditCard)
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="p-4 text-white bg-blue-500 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Limit Penarikan</h3>
            <p class="text-2xl">Rp {{ number_format($creditCard->cash_out_limit, 0, ', ', '.') }}</p>
        </div>
        <div class="p-4 text-white bg-orange-500 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Sudah Ditarik Bulan Ini</h3>
            <p class="text-2xl">Rp {{ number_format($creditCard->cash_out_used_this_month, 0, ', ', '.') }}</p>
        </div>
        <div class="p-4 text-white bg-green-600 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Sisa Limit Bulan Ini</h3>
            <p class="text-2xl">Rp {{ number_format($creditCard->cash_out_limit - $creditCard->cash_out_used_this_month, 0, ', ', '.') }}</p>
        </div>
    </div>
    @endif

    <h3 class="mb-4 text-xl font-semibold text-gray-700">Riwayat Penarikan Tunai</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Tanggal</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Deskripsi</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-right text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Jumlah</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ \Carbon\Carbon::parse($transaction->transaction_date)->isoFormat('D MMMM YYYY, HH:mm') }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $transaction->description ?? '-' }}</td>
                        <td class="px-5 py-5 text-sm text-right bg-white border-b border-gray-200">Rp {{ number_format($transaction->amount, 2, ', ', '.') }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight text-yellow-900">
                                <span aria-hidden class="absolute inset-0 bg-yellow-200 rounded-full opacity-50"></span>
                                <span class="relative">{{ ucfirst($transaction->status) }}</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-500">Belum ada riwayat penarikan tunai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white border-t">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-900 bg-opacity-50">
            <div class="w-11/12 max-w-lg mx-auto bg-white rounded-lg shadow-lg">
                <form wire:submit.prevent="store">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h4 class="text-lg font-semibold">Formulir Penarikan Tunai</h4>
                        <button wire:click="closeModal()" type="button" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label for="amount" class="block mb-2 text-sm font-bold text-gray-700">Jumlah Penarikan</label>
                            <input wire:model.defer="amount" type="number" id="amount" placeholder="Contoh: 500000" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                            @error('amount') <span class="mt-1 text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block mb-2 text-sm font-bold text-gray-700">Keterangan (Opsional)</label>
                            <textarea wire:model.defer="description" id="description" rows="3" placeholder="Contoh: Untuk keperluan pribadi" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"></textarea>
                            @error('description') <span class="mt-1 text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end p-4 bg-gray-50">
                        <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">Ajukan</button>
                        <button type="button" wire:click="closeModal()" class="px-4 py-2 ml-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
