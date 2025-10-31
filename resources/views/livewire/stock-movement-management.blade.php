<div>
    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Histori Pergerakan Stok</h2>

                    <div class="flex justify-end items-center mb-4">
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk atau lokasi..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($stockMovements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movement->movement_date->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movement->product->product_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movement->location->location_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($movement->movement_type == 'in') bg-green-100 text-green-800 @elseif($movement->movement_type == 'out') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($movement->movement_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $movement->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($movement->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $movement->reference_type ? (class_basename($movement->reference_type) . ' #' . $movement->reference_id) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movement->createdBy->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada data pergerakan stok ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $stockMovements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>