
<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Buat Penerimaan Barang (GRN) Baru</h2>

                    <!-- Header Form -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 border-b pb-6">
                        <div>
                            <x-input-label for="receipt_date" value="Tanggal Penerimaan" />
                            <x-text-input id="receipt_date" type="date" class="mt-1 block w-full" wire:model="receipt_date" />
                            @error('receipt_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-input-label for="location_id" value="Lokasi Penerimaan" />
                            <select id="location_id" wire:model="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
                                @endforeach
                            </select>
                            @error('location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-input-label for="reference_number" value="Nomor Referensi (Opsional)" />
                            <x-text-input id="reference_number" type="text" class="mt-1 block w-full" wire:model="reference_number" />
                        </div>
                    </div>

                    <!-- Product Search -->
                    <div class="mb-4 relative">
                        <x-input-label for="product_search" value="Cari & Tambah Produk" />
                        <x-text-input id="product_search" type="text" class="mt-1 block w-full" wire:model.live.debounce.300ms="product_search" placeholder="Ketik nama atau kode produk..." />
                        @if(count($searched_products) > 0)
                            <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 shadow-lg">
                                @foreach($searched_products as $product)
                                    <div wire:click="addProduct({{ $product->product_id }})" class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                        {{ $product->product_name }} ({{ $product->product_code }})
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Line Items -->
                    @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-48">Harga Beli Satuan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-48">Subtotal</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($items as $productId => $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $item['product_name'] }}</td>
                                        <td class="px-4 py-2">
                                            <x-text-input type="number" wire:model.live="items.{{ $productId }}.quantity" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <x-text-input type="number" wire:model.live="items.{{ $productId }}.price" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2">
                                            Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <x-danger-button wire:click="removeItem({{ $productId }})">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada produk yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end mt-6 border-t pt-6">
                        <a href="{{ route('pos.grn.index') }}">
                            <x-secondary-button>Batal</x-secondary-button>
                        </a>
                        <x-primary-button wire:click="save" class="ml-4">Simpan GRN</x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
