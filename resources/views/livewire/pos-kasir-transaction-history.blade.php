<div>
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto" id="transaction-history-table-container" tabindex="0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Kasir</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Pelanggan</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Total</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Metode</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Status</th>
                    <th scope="col" class="relative px-6 py-3 w-1/12 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi (D/B)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="transaction-history-tbody">
                @forelse ($transactions as $transaction)
                    <tr class="transaction-row {{ $transaction->status === 'voided' ? 'bg-gray-100 text-gray-500' : '' }}" data-transaction-id="{{ $transaction->transaction_id }}" tabindex="0">
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transaction->cashier->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($transaction->payment_method === 'credit_card')
                                {{ $transaction->customer->full_name ?? 'N/A' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if ($transaction->status === 'voided')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <x-secondary-button wire:click="viewDetails({{ $transaction->transaction_id }})" class="px-2 py-1 text-xs btn-detail">Detail</x-secondary-button>
                            @if ($transaction->status !== 'voided' && auth()->user()->can('void sales'))
                                <x-danger-button wire:click="confirmVoid({{ $transaction->transaction_id }})" class="px-2 py-1 text-xs btn-void">Batal</x-danger-button>
                            @endif
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
    <!-- Confirmation Modal for Voiding Transaction -->
    <x-confirmation-modal wire:model="confirmingVoid" id="confirm-void-transaction-pos-kasir">
        <x-slot name="title">
            {{ __('Batalkan Transaksi') }}
        </x-slot>
        <x-slot name="content">
            {{ __('Anda yakin ingin membatalkan transaksi ini? Stok akan dikembalikan dan limit kartu kredit (jika ada) akan dipulihkan.') }}
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="cancelVoid" wire:loading.attr="disabled">
                {{ __('Tidak') }}
            </x-secondary-button>
            <x-danger-button class="ml-3" wire:click="voidTransaction({{ $transactionToVoidId }})" wire:loading.attr="disabled">
                {{ __('Ya, Batalkan') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>

@push('scripts')
<style>
    .transaction-row.selected {
        outline: 2px solid #4f46e5; /* indigo-600 */
        outline-offset: -1px;
    }
    .transaction-row:focus {
        outline: none;
    }
</style>
<script>
    document.addEventListener('livewire:init', () => {
        const historyContainer = document.getElementById('transaction-history-table-container');
        let transactionRows = [];
        let selectedIndex = -1;

        const updateTransactionRows = () => {
            if(historyContainer) {
                transactionRows = historyContainer.querySelectorAll('.transaction-row');
            }
        };

        const updateHighlight = () => {
            transactionRows.forEach((row, index) => {
                if (index === selectedIndex) {
                    row.classList.add('selected');
                    row.scrollIntoView({ block: 'nearest' });
                } else {
                    row.classList.remove('selected');
                }
            });
        };

        const resetSelection = () => {
            selectedIndex = -1;
            updateTransactionRows();
            updateHighlight();
        };

        // Initial load
        updateTransactionRows();

        // Reset selection when Livewire updates the DOM
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (component.name === 'pos-kasir-transaction-history') {
                resetSelection();
            }
        });
        
        if(historyContainer) {
            historyContainer.addEventListener('focus', () => {
                if(selectedIndex === -1 && transactionRows.length > 0) {
                    selectedIndex = 0;
                    updateHighlight();
                }
            });

            historyContainer.addEventListener('keydown', (event) => {
                switch (event.key) {
                    case 'ArrowDown':
                        event.preventDefault();
                        if (selectedIndex < transactionRows.length - 1) {
                            selectedIndex++;
                        } else {
                            selectedIndex = 0; // Loop
                        }
                        updateHighlight();
                        break;
                    case 'ArrowUp':
                        event.preventDefault();
                        if (selectedIndex > 0) {
                            selectedIndex--;
                        } else {
                            selectedIndex = transactionRows.length - 1; // Loop
                        }
                        updateHighlight();
                        break;
                    case 'd':
                    case 'D':
                        if (selectedIndex !== -1 && transactionRows[selectedIndex]) {
                            event.preventDefault();
                            const detailButton = transactionRows[selectedIndex].querySelector('.btn-detail');
                            detailButton?.click();
                        }
                        break;
                    case 'b':
                    case 'B':
                         if (selectedIndex !== -1 && transactionRows[selectedIndex]) {
                            event.preventDefault();
                            const voidButton = transactionRows[selectedIndex].querySelector('.btn-void');
                            voidButton?.click();
                        }
                        break;
                }
            });
        }
    });
</script>
@endpush