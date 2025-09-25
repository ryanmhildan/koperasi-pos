
<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Harga</h2>

                    <div class="flex justify-end items-center mb-4">
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual Default</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-primary-button wire:click="managePrices({{ $product->product_id }})">Kelola Harga</x-primary-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Produk tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Management Modal -->
    <x-modal name="price-management-modal" :show="$showPriceModal" maxWidth="5xl">
        <x-slot name="title">
            Kelola Harga untuk: {{ $selectedProduct?->product_name ?? '' }}
        </x-slot>

        <x-slot name="content">
            @if (session()->has('message'))
                <div class="bg-green-100 border-green-400 text-green-700 border-l-4 p-4 mb-4" role="alert">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Cost Prices Section -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Harga Pokok (Biaya)</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <form wire:submit.prevent="saveCostPrice" class="flex items-end space-x-2 mb-4">
                            <div>
                                <label class="text-sm">Lokasi</label>
                                <select wire:model="editingCostPrice.location_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('editingCostPrice.location_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-sm">Harga Pokok</label>
                                <input type="number" wire:model="editingCostPrice.average_price" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                @error('editingCostPrice.average_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <x-primary-button type="submit">Simpan</x-primary-button>
                        </form>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr><th class="px-2 py-1 text-left text-xs font-medium">Lokasi</th><th class="px-2 py-1 text-left text-xs font-medium">Harga</th></tr>
                            </thead>
                            <tbody>
                                @forelse($costPrices as $price)
                                    <tr><td class="px-2 py-1">{{ $price->location->name }}</td><td class="px-2 py-1">Rp {{ number_format($price->average_price, 0, ',', '.') }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="px-2 py-1 text-center text-gray-500">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Selling Prices Section -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Harga Jual Khusus</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                         <form wire:submit.prevent="saveSellingPrice" class="flex items-end space-x-2 mb-4">
                            <div>
                                <label class="text-sm">Lokasi</label>
                                <select wire:model="editingSellingPrice.location_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('editingSellingPrice.location_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-sm">Harga Jual</label>
                                <input type="number" wire:model="editingSellingPrice.selling_price" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                @error('editingSellingPrice.selling_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label class="text-sm">Diskon (%)</label>
                                <input type="number" wire:model="editingSellingPrice.discount" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                @error('editingSellingPrice.discount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <x-primary-button type="submit">Simpan</x-primary-button>
                        </form>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr><th class="px-2 py-1 text-left text-xs font-medium">Lokasi</th><th class="px-2 py-1 text-left text-xs font-medium">Harga</th><th class="px-2 py-1 text-left text-xs font-medium">Diskon</th></tr>
                            </thead>
                            <tbody>
                                @forelse($sellingPrices as $price)
                                    <tr><td class="px-2 py-1">{{ $price->location->name }}</td><td class="px-2 py-1">Rp {{ number_format($price->selling_price, 0, ',', '.') }}</td><td class="px-2 py-1">{{ $price->discount }}%</td></tr>
                                @empty
                                    <tr><td colspan="3" class="px-2 py-1 text-center text-gray-500">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Tutup
            </x-secondary-button>
        </x-slot>
    </x-modal>
</div>
