<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi POS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <x-input-label for="startDate" value="Tanggal Mulai" />
                        <x-text-input id="startDate" type="date" class="mt-1 block w-full" wire:model.live="startDate" />
                    </div>
                    <div>
                        <x-input-label for="endDate" value="Tanggal Akhir" />
                        <x-text-input id="endDate" type="date" class="mt-1 block w-full" wire:model.live="endDate" />
                    </div>
                    <div>
                        <x-input-label for="search" value="Cari No. Transaksi / Kasir" />
                        <x-text-input id="search" type="text" class="mt-1 block w-full" wire:model.live.debounce.300ms="search" placeholder="Pencarian..." />
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->cashier->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($transaction->payment_method === 'credit_card')
                                            {{ $transaction->customer->full_name ?? 'N/A' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button wire:click="viewDetails({{ $transaction->transaction_id }})">Detail</x-secondary-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada transaksi ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    @if ($selectedTransaction)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Detail Transaksi: {{ $selectedTransaction->transaction_number }}
                                </h3>
                                <div class="mt-4 text-sm text-gray-600">
                                    <p><strong>Tanggal:</strong> {{ $selectedTransaction->created_at->format('d M Y, H:i') }}</p>
                                    <p><strong>Kasir:</strong> {{ $selectedTransaction->cashier->full_name ?? 'N/A' }}</p>
                                    @if($selectedTransaction->payment_method === 'credit_card')
                                    <p><strong>Pelanggan:</strong> {{ $selectedTransaction->customer->full_name ?? 'N/A' }}</p>
                                    @endif
                                    <p><strong>Metode Pembayaran:</strong> {{ ucfirst(str_replace('_', ' ', $selectedTransaction->payment_method)) }}</p>
                                </div>
                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($selectedTransaction->details as $detail)
                                                <tr>
                                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->product->product_name ?? 'Produk Dihapus' }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-center">{{ $detail->quantity }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-right">Rp {{ number_format($detail->selling_price, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-right">Rp {{ number_format($detail->total_price, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="px-4 py-2 text-right font-bold">Total Akhir</td>
                                                <td class="px-4 py-2 text-right font-bold">Rp {{ number_format($selectedTransaction->total_amount, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" wire:click="closeModal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>