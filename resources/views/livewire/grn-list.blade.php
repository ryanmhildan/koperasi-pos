
<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold">Penerimaan Barang (GRN)</h2>
                        <a href="{{ route('pos.grn.create') }}">
                            <x-primary-button>Buat GRN Baru</x-primary-button>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($grns as $grn)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grn->grn_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grn->receipt_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grn->location->location_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $grn->reference_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="viewDetails({{ $grn->grn_id }})">Lihat Detail</x-secondary-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data GRN.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $grns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRN Details Modal -->
    <x-modal name="grn-details-modal" maxWidth="3xl">
        @if ($selectedGrn)
            <div class="p-6">
                <h2 class="text-2xl font-semibold mb-4">Detail GRN: #{{ $selectedGrn->grn_id }}</h2>

                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                    <div>
                        <p><span class="font-semibold">Tanggal:</span> {{ $selectedGrn->receipt_date->format('d M Y') }}</p>
                        <p><span class="font-semibold">Lokasi:</span> {{ $selectedGrn->location->location_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p><span class="font-semibold">No. Referensi:</span> {{ $selectedGrn->reference_number }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold mb-2">Produk:</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($selectedGrn->details as $detail)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->product->product_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">Tidak ada produk dalam GRN ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="closeModal">
                        Tutup
                    </x-secondary-button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
