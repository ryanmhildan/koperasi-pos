<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Histori Shift Kasir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8"> {{-- Changed max-w-7xl to max-w-full --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <x-input-label for="startDate" value="Tanggal Mulai" />
                        <x-text-input id="startDate" type="date" class="mt-1 block w-full" wire:model.live="startDate" />
                    </div>
                    <div>
                        <x-input-label for="endDate" value="Tanggal Akhir" />
                        <x-text-input id="endDate" type="date" class="mt-1 block w-full" wire:model.live="endDate" />
                    </div>
                    <div>
                        <x-input-label for="selectedUserId" value="Kasir" />
                        <select id="selectedUserId" wire:model.live="selectedUserId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua Kasir</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Shifts Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mulai Shift</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai Shift</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Awal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Penjualan Tunai</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Akhir</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($shifts as $shift)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $shift->shift_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $shift->user->full_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $shift->shift_start ? \Carbon\Carbon::parse($shift->shift_start)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $shift->shift_end ? \Carbon\Carbon::parse($shift->shift_end)->format('H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($shift->closing_balance, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($shift->status == 'open')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Terbuka</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Tutup</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data shift ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $shifts->links() }}
                </div>

            </div>
        </div>
    </div>
</div>