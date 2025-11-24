<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if ($pinjaman)
                {{ __('Manajemen Angsuran untuk Pinjaman #' . $pinjaman->pinjaman_id) }}
            @else
                {{ __('Manajemen Angsuran') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($pinjaman)
                        {{-- View for a specific loan's installments --}}
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <div class="text-lg"><strong>Peminjam:</strong> {{ $pinjaman->user->full_name }} ({{$pinjaman->user->nrp}})</div>
                                <div class="text-md"><strong>Jumlah:</strong> Rp {{ number_format($pinjaman->loan_amount, 0, ',', '.') }}</div>
                                <div class="text-md"><strong>Sisa:</strong> Rp {{ number_format($pinjaman->remaining_balance, 0, ',', '.') }}</div>
                            </div>
                            <x-secondary-button href="{{ route('koperasi.angsuran') }}" wire:navigate>
                                &larr; Kembali ke Daftar Pinjaman
                            </x-secondary-button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($angsuran as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->due_date)->isoFormat('D MMM YYYY') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($item->status == 'paid') bg-green-100 text-green-800
                                                    @elseif($item->status == 'pending' && $item->due_date < now()) bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ $item->status == 'pending' && $item->due_date < now() ? 'Overdue' : ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($item->status === 'pending')
                                                    <x-primary-button wire:click="requestMarkAsPaid({{ $item->angsuran_id }})">Tandai Lunas</x-primary-button>
                                                @else
                                                    <span class="text-gray-500">Lunas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada data angsuran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- View for listing active loans --}}
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-gray-600">Pilih pinjaman untuk melihat atau mengelola angsuran.</div>
                            <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Peminjam/NRP/ID Pinjaman..." class="w-1/3" />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pinjaman</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Pinjaman</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjaman</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($pinjamanList as $loan)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $loan->user->full_name }}</div>
                                                <div class="text-sm text-gray-500">ID Pinjaman: #{{ $loan->pinjaman_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($loan->loan_amount, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($loan->loan_date)->isoFormat('D MMM YYYY') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button href="{{ route('koperasi.angsuran', ['pinjamanId' => $loan->pinjaman_id]) }}" wire:navigate>
                                                    Lihat Angsuran
                                                </x-secondary-button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada pinjaman aktif.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $pinjamanList->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
