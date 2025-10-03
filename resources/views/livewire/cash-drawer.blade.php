<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-4">Manajemen Shift Kasir</h2>

                @if (session()->has('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($activeDrawer)
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                        <p class="font-bold">Shift Sedang Aktif</p>
                        <p>Shift dimulai pada: {{ $activeDrawer->shift_start->format('d M Y, H:i') }}</p>
                        <p>Saldo Awal: Rp {{ number_format($activeDrawer->opening_balance, 0, ',', '.') }}</p>
                        {{-- TODO: Add close shift functionality --}}
                        <x-danger-button class="mt-4" wire:click="closeShift" wire:confirm="Apakah Anda yakin ingin menutup shift ini? Semua transaksi akan difinalisasi.">Tutup Shift</x-danger-button>
                    </div>
                @else
                    <div>
                        <p class="mb-4">Tidak ada shift yang aktif. Silakan buka shift baru untuk memulai.</p>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="location_id" value="Lokasi Kasir" />
                                <select id="location_id" wire:model.defer="location_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <x-input-label for="opening_balance" value="Saldo Awal (Uang Modal)" />
                                <x-text-input id="opening_balance" type="number" class="mt-1 block w-full" wire:model.defer="opening_balance" placeholder="Contoh: 500000" />
                                @error('opening_balance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <x-primary-button wire:click="openShift">
                                Buka Shift
                            </x-primary-button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>