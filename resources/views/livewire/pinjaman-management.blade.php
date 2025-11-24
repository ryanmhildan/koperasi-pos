<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pinjaman') }}
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

                    <div class="flex justify-between items-center mb-4">
                        <x-primary-button wire:click="create">
                            Tambah Pinjaman
                        </x-primary-button>
                        <div class="flex items-center space-x-2">
                             <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..." class="w-80" />
                            <select wire:model.live="statusFilter" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="active">Aktif</option>
                                <option value="closed">Lunas</option>
                            </select>
                            <select wire:model.live="loanTypeFilter" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua Tipe</option>
                                @foreach($loanTypes as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Pinjaman</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Saldo</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pinjaman as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->user->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->user->nrp }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($item->loan_amount, 0, ',', '.') }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->tenor_months }} bulan @ {{ $item->interest_rate }}%</div>
                                            <div class="text-sm text-gray-500">{{ ucfirst($item->loan_type) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->loan_date)->isoFormat('D MMM YYYY') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono text-gray-800">Rp {{ number_format($item->remaining_balance, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($item->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('active') bg-green-100 text-green-800 @break
                                                    @case('closed') bg-blue-100 text-blue-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ $item->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($item->status === 'pending')
                                                 <button wire:click="requestApproval({{ $item->pinjaman_id }})" class="font-medium text-green-600 hover:text-green-900">Setujui</button>
                                            @else
                                                <a href="{{ route('koperasi.angsuran', ['pinjamanId' => $item->pinjaman_id]) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Angsuran</a>
                                            @endif
                                            <button wire:click="edit({{ $item->pinjaman_id }})" class="ml-4 font-medium text-yellow-600 hover:text-yellow-900">Edit</button>
                                            <button wire:click="requestDeletion({{ $item->pinjaman_id }})" class="ml-4 font-medium text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada data pinjaman ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pinjaman->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- The modal for create/edit is in its own component: livewire:pinjaman-form-modal --}}
    {{-- The modal for approval confirmation is in its own component: livewire:approval-confirmation-modal --}}
</div>
