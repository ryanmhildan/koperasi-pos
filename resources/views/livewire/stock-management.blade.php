<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Stok</h2>

                    <div class="flex justify-end items-center mb-4">
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Update</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($stocks as $stock)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $stock->product->product_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $stock->product->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $stock->location->location_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $stock->current_stock }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $stock->last_updated ? $stock->last_updated->format('d M Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="viewHistory({{ $stock->stock_id }})">Lihat Riwayat</x-secondary-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Stok produk tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $stocks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Modal -->
    <x-modal name="stock-history-modal" maxWidth="4xl">
        <x-slot name="title">
            Riwayat Stok: {{ $selectedProduct?->product_name ?? '' }}
        </x-slot>

        <x-slot name="content">
            <div class="overflow-x-auto max-h-96">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($stockMovements as $movement)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $movement->movement_date->format('d M Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $movement->movement_type }}</td>
                                <td class="px-4 py-2 whitespace-nowrap font-semibold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $movement->reference_type }}: {{ $movement->reference_id }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $movement->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">Tidak ada riwayat pergerakan stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Tutup
            </x-secondary-button>
        </x-slot>
    </x-modal>
</div>