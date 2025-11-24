<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Angsuran Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Loan Selection --}}
                    <div class="mb-4">
                        <label for="pinjaman_id" class="block font-medium text-sm text-gray-700">Pilih Pinjaman</label>
                        <select id="pinjaman_id" wire:model.live="pinjaman_id" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Pilih Pinjaman --</option>
                            @foreach($loans as $loan)
                                <option value="{{ $loan->pinjaman_id }}">
                                    Pinjaman #{{ $loan->pinjaman_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($pinjaman)
                        {{-- Payment Form --}}
                        <div class="mb-4 p-4 border rounded-md">
                            <h3 class="font-semibold text-lg mb-2">Pembayaran Angsuran</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Sisa Pinjaman</label>
                                    <p class="mt-1 text-lg font-bold">Rp {{ number_format($pinjaman->remaining_balance, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Jumlah Angsuran Bulanan</label>
                                    <p class="mt-1 text-lg font-bold">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-primary-button wire:click="payAngsuran">
                                    Bayar Angsuran
                                </x-primary-button>
                            </div>
                        </div>

                        {{-- Installment List --}}
                        <div>
                            <h3 class="font-semibold text-lg mb-2">Riwayat Angsuran</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($installments as $installment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($installment->due_date)->isoFormat('D MMM YYYY') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($installment->amount, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($installment->status == 'paid') bg-green-100 text-green-800
                                                        @elseif($installment->status == 'pending' && $installment->due_date < now()) bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ $installment->status == 'pending' && $installment->due_date < now() ? 'Overdue' : ucfirst($installment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $installment->paid_date ? \Carbon\Carbon::parse($installment->paid_date)->isoFormat('D MMM YYYY') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                                    Tidak ada data angsuran untuk pinjaman ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @livewire('pay-angsuran-confirmation-modal')
</div>