<div class="p-6 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Manajemen Angsuran</h2>
    </div>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari berdasarkan status, tanggal, atau jumlah..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        No.
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Jatuh Tempo
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Jumlah Pokok
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Denda
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Total Bayar
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Status
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($angsurans as $index => $angsuran)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            {{ $angsurans->firstItem() + $index }}
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            {{ \Carbon\Carbon::parse($angsuran->due_date)->isoFormat('D MMMM YYYY') }}
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            Rp {{ number_format($angsuran->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            Rp {{ number_format($angsuran->denda, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 text-sm font-semibold bg-white border-b border-gray-200">
                            Rp {{ number_format($angsuran->total_amount, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight text-{{ $angsuran->status_badge_color }}-900">
                                <span aria-hidden class="absolute inset-0 bg-{{ $angsuran->status_badge_color }}-200 rounded-full opacity-50"></span>
                                <span class="relative">{{ $angsuran->status_text }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            @if (in_array($angsuran->status, ['pending', 'overdue']))
                                <button wire:click="pay('{{ $angsuran->angsuran_id }}')" 
                                        wire:loading.attr="disabled"
                                        wire:target="pay('{{ $angsuran->angsuran_id }}')"
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded-full hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue active:bg-blue-800 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="pay('{{ $angsuran->angsuran_id }}')">Bayar Sekarang</span>
                                    <span wire:loading wire:target="pay('{{ $angsuran->angsuran_id }}')">Memproses...</span>
                                </button>
                            @else
                                <span class="px-4 py-2 font-semibold text-gray-500">Lunas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-500">
                            Tidak ada data angsuran untuk ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-5 py-5 bg-white border-t">
            {{ $angsurans->links() }}
        </div>
    </div>
</div>