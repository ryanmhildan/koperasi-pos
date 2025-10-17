<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-6 md:flex-row md:space-y-0 md:space-x-6">
                
                <!-- Kolom Navigasi Kiri -->
                <div class="w-full md:w-1/4">
                    <div class="p-4 bg-white rounded-lg shadow">
                        <nav class="space-y-1">
                            <!-- Menu Umum -->
                            <a href="#" wire:click.prevent="$set('activeTab', 'profil')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'profil' ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                                Profil Saya
                            </a>
                            <a href="#" wire:click.prevent="$set('activeTab', 'password')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'password' ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                                Ganti Password
                            </a>

                            <!-- Menu Anggota -->
                            @role('Anggota')
                            <a href="#" wire:click.prevent="$set('activeTab', 'simpanan')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'simpanan' ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                                Simpanan Saya
                            </a>
                            <a href="#" wire:click.prevent="$set('activeTab', 'pinjaman')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'pinjaman' ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                                Pinjaman Saya
                            </a>
                            @endrole

                            <!-- Menu Kasir -->
                            @role('Kasir')
                            <a href="#" wire:click.prevent="$set('activeTab', 'transaksi')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'transaksi' ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                                Riwayat Transaksi
                            </a>
                            @endrole

                            <!-- Menu Admin -->
                            @role('Admin')
                            <a href="#" wire:click.prevent="$set('activeTab', 'hapus')" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $activeTab === 'hapus' ? 'bg-gray-200 text-red-700' : 'text-red-600 hover:bg-red-50' }}">
                                Hapus Akun
                            </a>
                            @endrole
                        </nav>
                    </div>
                </div>

                <!-- Kolom Konten Kanan -->
                <div class="w-full md:w-3/4">
                    <div class="p-4 bg-white rounded-lg shadow sm:p-8">
                        <div class="max-w-xl">
                            @if ($activeTab === 'profil')
                                <livewire:profile.update-profile-information-form />
                            @elseif ($activeTab === 'password')
                                <livewire:profile.update-password-form />
                            @elseif ($activeTab === 'simpanan')
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Simpanan Saya</h3>
                                    <p class="mt-1 text-sm text-gray-600">Fitur ini sedang dalam pengembangan.</p>
                                </div>
                            @elseif ($activeTab === 'pinjaman')
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Pinjaman Saya</h3>
                                    <p class="mt-1 text-sm text-gray-600">Fitur ini sedang dalam pengembangan.</p>
                                </div>
                            @elseif ($activeTab === 'transaksi')
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Riwayat Transaksi</h3>
                                    <p class="mt-1 text-sm text-gray-600">Fitur ini sedang dalam pengembangan.</p>
                                </div>
                            @elseif ($activeTab === 'hapus')
                                <livewire:profile.delete-user-form />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
