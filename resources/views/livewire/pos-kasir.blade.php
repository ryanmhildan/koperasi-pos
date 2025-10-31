<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:pb-6 lg:pb-8 pt-2 sm:pt-3 lg:pt-4">
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

    @if (!$cashDrawer)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-xl mx-auto">
            <h2 class="text-2xl font-semibold mb-4 text-center">Buka Shift Kasir</h2>
            <p class="mb-4 text-center text-gray-600">Anda harus membuka shift untuk memulai transaksi.</p>
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="location_id" value="Lokasi Kasir" />
                    <select id="location_id" wire:model.defer="location_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="opening_balance" value="Saldo Awal (Uang Modal)" />
                    <x-text-input id="opening_balance" type="number" class="mt-1 block w-full" wire:model.defer="opening_balance" placeholder="Contoh: 500000" />
                    @error('opening_balance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center space-x-2">
                    <x-primary-button id="btn-open-shift" wire:click="openShift" class="w-full justify-center">
                        Buka Shift
                    </x-primary-button>
                    <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F9</kbd>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Cart -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-4 flex flex-col h-full">
                <div class="mb-4">
                    <div class="flex items-center space-x-2">
                        <x-input-label for="customer_search" value="Pelanggan (untuk bayar kredit)" />
                        <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F2</kbd>
                    </div>
                    @if ($selected_customer)
                        <div class="flex items-center justify-between mt-1 p-2 bg-gray-100 rounded-md">
                            <div>
                                <p class="font-semibold">{{ $selected_customer->full_name }}</p>
                                <p class="text-sm text-gray-600">NRP: {{ $selected_customer->nrp }}</p>
                                @if($customer_credit_info)
                                    <p class="text-sm font-semibold {{ str_contains($customer_credit_info, 'Tidak ada') ? 'text-red-500' : 'text-blue-600' }}">{{ $customer_credit_info }}</p>
                                @endif
                            </div>
                            <button wire:click="clearCustomer" class="text-red-500 hover:text-red-700 font-bold text-xl">&times;</button>
                        </div>
                    @else
                        <div class="relative">
                            <x-text-input id="customer_search" type="text" class="mt-1 block w-full" wire:model.live.debounce.300ms="customer_search" placeholder="Cari nama atau NRP..." />
                            @if(count($searched_customers) > 0)
                                <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 shadow-lg">
                                    @foreach($searched_customers as $customer)
                                        <div wire:click="selectCustomer({{ $customer->user_id }})" class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                            {{ $customer->full_name }} ({{ $customer->nrp }})
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <h2 class="text-xl font-semibold mb-4">Keranjang</h2>
                <div class="flex-grow overflow-y-auto">
                    @forelse ($cart as $id => $item)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <p class="font-semibold">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center">
                                <button wire:click="decreaseQuantity('{{ $id }}')" class="px-2 py-1 border border-gray-300 rounded-l-md bg-gray-50 hover:bg-gray-100">-</button>
                                <span class="px-4 py-1 border-t border-b border-gray-300">{{ $item['quantity'] }}</span>
                                <button wire:click="increaseQuantity('{{ $id }}')" class="px-2 py-1 border border-gray-300 rounded-r-md bg-gray-50 hover:bg-gray-100">+</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Keranjang kosong</p>
                    @endforelse
                </div>
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center space-x-2">
                            <x-input-label for="cash_received" value="Uang Pembeli" />
                            <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F3</kbd>
                        </div>
                        <x-text-input id="cash_received" type="number" class="mt-1 block w-full" wire:model.live="cashReceived" placeholder="Masukkan jumlah uang" />
                    </div>

                    <div class="mt-4 flex justify-between font-bold text-lg">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center space-x-2">
                            <x-primary-button id="btn-process-cash" wire:click="confirmTransaction('cash')" class="w-full justify-center" :disabled="empty($cart)">
                                Proses Cash
                            </x-primary-button>
                            <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F4</kbd>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-primary-button id="btn-process-credit" wire:click="confirmTransaction('credit_card')" class="w-full justify-center" :disabled="empty($cart) || !$selected_customer">
                                Proses Kartu Kredit
                            </x-primary-button>
                            <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F6</kbd>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center space-x-2"> {{-- New div for history button --}}
                        <x-secondary-button id="btn-transaction-history" wire:click="openTransactionHistoryModal" class="w-full justify-center">
                            Histori Transaksi
                        </x-secondary-button>
                        <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F7</kbd>
                    </div>

                </div>
            </div>

            <!-- Right Column: Products -->
            <div class="lg:col-span-2">
                <div class="mb-4 flex items-start space-x-4">
                    <div class="flex-grow flex items-center space-x-2">
                        <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F1</kbd>
                        <x-text-input id="product-search-bar" wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk berdasarkan nama, kode, atau barcode..." class="w-full" />
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-danger-button id="btn-close-shift" wire:click="confirmCloseShift" class="whitespace-nowrap">
                            Tutup Shift
                        </x-danger-button>
                        <kbd class="font-sans text-sm font-semibold text-gray-500 border border-gray-300 rounded-md px-2 py-1">F8</kbd>
                    </div>
                </div>
                <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[75vh] overflow-y-auto p-2 bg-gray-50 rounded-lg" tabindex="0">
                    @forelse ($products as $product)
                <div wire:click="addToCart({{ $product['product_id'] }}, {{ (float)$product['location_selling_price'] }})" data-product-id="{{ $product['product_id'] }}" data-product-price="{{ $product['location_selling_price'] }}" class="product-item cursor-pointer border rounded-lg p-3 bg-white hover:shadow-lg transition-shadow duration-200 flex flex-col justify-between aspect-square">
                            <div>
                                <p class="font-bold text-sm">{{ $product['product_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $product['category_name'] ?? '' }}</p>
                            </div>
                            <p class="text-right font-semibold text-indigo-600 mt-2">Rp {{ number_format($product['location_selling_price'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-gray-500">Produk tidak ditemukan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <x-confirmation-modal id="confirm-transaction" wire:model.live="confirmingTransaction">
        <x-slot name="title">
            Konfirmasi Transaksi
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin memproses transaksi ini?
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingTransaction', false)" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-3" wire:click="processTransaction" wire:loading.attr="disabled">
                Proses
            </x-primary-button>
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal id="confirm-close-shift" wire:model.live="confirmingCloseShift">
        <x-slot name="title">
            Konfirmasi Tutup Shift
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin menutup shift ini? Semua transaksi akan difinalisasi.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingCloseShift', false)" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="closeShift" wire:loading.attr="disabled">
                Tutup Shift
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <x-modal name="transaction-history-modal" maxWidth="7xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">
                Histori Transaksi Terakhir
            </h2>

            <!-- This is where the transaction history content will go -->
            @livewire('pos-kasir-transaction-history') {{-- New Livewire component for history --}}

            <div class="flex justify-end items-center mt-4 space-x-2">
                <kbd class="font-sans text-sm font-semibold text-gray-500">F7</kbd>
                <x-secondary-button wire:click="closeTransactionHistoryModal">
                    Tutup
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>

@push('scripts')
<style>
    .product-item.selected {
        outline: 2px solid #4f46e5; /* indigo-600 */
        outline-offset: -1px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    kbd {
        display: inline-block;
        padding: 0.1em 0.3em;
        font-family: inherit;
        font-size: 0.8em;
        font-weight: 600;
        line-height: 1;
        color: #4b5563; /* gray-600 */
        white-space: nowrap;
        background-color: #f3f4f6; /* gray-100 */
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.25rem;
    }
</style>
<script>
    document.addEventListener('livewire:init', () => {
        const searchInput = document.getElementById('product-search-bar');
        const customerSearchInput = document.getElementById('customer_search');
        const cashReceivedInput = document.getElementById('cash_received');
        const productGrid = document.getElementById('product-grid');
        
        let selectedIndex = -1;
        let productItems = [];

        const updateProductItems = () => {
            if (productGrid) {
                productItems = productGrid.querySelectorAll('.product-item');
            }
        };

        const updateHighlight = () => {
            productItems.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('selected');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('selected');
                }
            });
        };
        
        const resetSelection = () => {
            selectedIndex = -1;
            updateProductItems();
            updateHighlight();
        };

        // Initial load
        updateProductItems();

        // Reset selection when Livewire updates the DOM
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (component.id === @this.id && el.id === 'product-grid') {
                resetSelection();
            }
        });

        // Add event listener for focusing on the search bar
        if(searchInput) {
            searchInput.addEventListener('focus', () => {
                @this.set('search', '');
                resetSelection();
            });
        }

        document.addEventListener('keydown', function (event) {
            // Shortcuts that should work everywhere
            switch (event.key) {
                case 'F1':
                    event.preventDefault();
                    searchInput?.focus();
                    break;
                case 'F2':
                    event.preventDefault();
                    customerSearchInput?.focus();
                    break;
                case 'F3':
                    event.preventDefault();
                    cashReceivedInput?.focus();
                    break;
                case 'F4':
                    event.preventDefault();
                    document.getElementById('btn-process-cash')?.click();
                    break;
                case 'F6':
                    event.preventDefault();
                    document.getElementById('btn-process-credit')?.click();
                    break;
                case 'F8':
                    event.preventDefault();
                    document.getElementById('btn-close-shift')?.click();
                    break;
                case 'F9':
                    event.preventDefault();
                    document.getElementById('btn-open-shift')?.click();
                    break;
                case 'F7':
                    event.preventDefault();
                    if (@this.get('showTransactionHistoryModal')) {
                        @this.call('closeTransactionHistoryModal');
                    } else {
                        @this.call('openTransactionHistoryModal');
                    }
                    break;
            }

            const activeElement = document.activeElement;

            // Navigate product grid when it or the search input is focused
            if (productGrid && (activeElement === productGrid || activeElement === searchInput)) {
                switch (event.key) {
                    case 'ArrowRight':
                    case 'ArrowDown':
                        event.preventDefault();
                        if (selectedIndex < productItems.length - 1) {
                            selectedIndex++;
                        } else {
                            selectedIndex = 0; // Loop to top
                        }
                        updateHighlight();
                        break;
                    case 'ArrowLeft':
                    case 'ArrowUp':
                        event.preventDefault();
                        if (selectedIndex > 0) {
                            selectedIndex--;
                        } else {
                            selectedIndex = productItems.length - 1; // Loop to bottom
                        }
                        updateHighlight();
                        break;
                    case 'Enter':
                        if (selectedIndex !== -1 && productItems[selectedIndex]) {
                            event.preventDefault();
                            event.stopImmediatePropagation();
                            const productId = productItems[selectedIndex].dataset.productId;
                            const price = productItems[selectedIndex].dataset.productPrice;
                            @this.call('addToCart', productId, price);
                        }
                        break;
                }
            }
        });

        // Focus the grid when navigating from search
        if(searchInput) {
            searchInput.addEventListener('keydown', (event) => {
                if (['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(event.key)) {
                    event.preventDefault();
                    productGrid.focus();
                    if (selectedIndex === -1) {
                        selectedIndex = 0;
                        updateHighlight();
                    }
                }
            });
        }
    });
</script>
@endpush