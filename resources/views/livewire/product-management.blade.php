<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Produk</h2>

                    @if (session()->has('message'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <x-primary-button wire:click="create" type="button">
                            Tambah Produk
                        </x-primary-button>
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->category->category_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->unit->unit_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($product->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="edit({{ $product->product_id }})">Edit</x-secondary-button>
                                            <x-danger-button wire:click="delete({{ $product->product_id }})" wire:confirm="Anda yakin ingin menghapus produk ini?">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada produk ditemukan.
                                        </td>
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

    <!-- Create/Edit Product Modal -->
    <x-modal name="product-form-modal" maxWidth="2xl">
        <form wire:submit.prevent="save" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Edit Produk' : 'Tambah Produk' }}
            </h2>

            @if ($errors->any())
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="product_code" value="Kode Produk" />
                    <x-text-input wire:model.defer="product_code" id="product_code" type="text" class="mt-1 block w-full" />
                    @error('product_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="barcode" value="Barcode" />
                    <x-text-input wire:model.defer="barcode" id="barcode" type="text" class="mt-1 block w-full" />
                    @error('barcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="product_name" value="Nama Produk" />
                    <x-text-input wire:model.defer="product_name" id="product_name" type="text" class="mt-1 block w-full" />
                    @error('product_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="category_id" value="Kategori" />
                    <select wire:model.defer="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="unit_id" value="Unit" />
                    <select wire:model.defer="unit_id" id="unit_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->unit_id }}">{{ $unit->unit_name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="selling_price" value="Harga Jual (Opsional)" />
                    <x-text-input wire:model.defer="selling_price" id="selling_price" type="number" step="any" class="mt-1 block w-full" />
                    @error('selling_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="minimum_stock" value="Stok Minimum" />
                    <x-text-input wire:model.defer="minimum_stock" id="minimum_stock" type="number" class="mt-1 block w-full" />
                    @error('minimum_stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="product_type" value="Tipe Produk" />
                    <select wire:model.defer="product_type" id="product_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach ($productTypes as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('product_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 flex items-center space-x-6 pt-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.defer="is_stock_item" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Item Stok</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.defer="track_expiry" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Lacak Kadaluarsa</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.defer="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal" type="button">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    Simpan
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>