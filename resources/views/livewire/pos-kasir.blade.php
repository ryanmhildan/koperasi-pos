
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
                <x-primary-button wire:click="openShift" class="w-full justify-center">
                    Buka Shift
                </x-primary-button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Cart -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-4 flex flex-col h-full">
                <h2 class="text-xl font-semibold mb-4">Keranjang</h2>
                <div class="flex-grow overflow-y-auto">
                    @forelse ($cart as $id => $item)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <p class="font-semibold">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center">
                                <input type="number" min="1" wire:model.live="cart.{{ $id }}.quantity" wire:change="updateQuantity('{{ $id }}', $event.target.value)" class="w-16 text-center border-gray-300 rounded-md shadow-sm">
                                <button wire:click="removeFromCart('{{ $id }}')" class="ml-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
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
                        <h3 class="font-semibold mb-2">Metode Pembayaran</h3>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model="paymentMethod" value="cash" class="form-radio">
                                <span class="ml-2">Cash</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model="paymentMethod" value="credit_card" class="form-radio">
                                <span class="ml-2">Kartu Kredit</span>
                            </label>
                        </div>
                    </div>
                    <x-primary-button wire:click="processTransaction" class="w-full mt-4 justify-center" :disabled="empty($cart)">
                        Proses Transaksi
                    </x-primary-button>
                    <x-danger-button wire:click="closeShift" wire:confirm="Apakah Anda yakin ingin menutup shift ini? Semua transaksi akan difinalisasi." class="w-full mt-2 justify-center">
                        Tutup Shift
                    </x-danger-button>
                </div>
            </div>

            <!-- Right Column: Products -->
            <div class="lg:col-span-2">
                <div class="mb-4">
                    <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk berdasarkan nama, kode, atau barcode..." class="w-full" />
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 h-[75vh] overflow-y-auto p-2 bg-gray-50 rounded-lg">
                    @forelse ($products as $product)
                        <div wire:click="addToCart({{ $product->product_id }})" class="cursor-pointer border rounded-lg p-3 bg-white hover:shadow-lg transition-shadow duration-200 flex flex-col justify-between">
                            <div>
                                <p class="font-bold text-sm">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->category->name ?? '' }}</p>
                            </div>
                            <p class="text-right font-semibold text-indigo-600 mt-2">Rp {{ number_format($product->location_selling_price, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-gray-500">Produk tidak ditemukan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
