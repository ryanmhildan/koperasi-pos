<div class="max-w-5xl mx-auto pt-18 pb-8">
    <!-- Welcome Header (Centered) -->
    <div class="mb-10 pt-6 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Hallo, {{ auth()->user()->full_name }}!</h1>
        <p class="mt-2 text-lg text-gray-500">Selamat datang kembali.</p>
    </div>

    <!-- Search Bar Placeholder (Centered) -->
    <div class="mb-12 px-6">
        <div class="relative max-w-lg mx-auto">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                    <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
            <input type="text" placeholder="Cari menu atau fitur..." class="w-full py-3 pl-10 pr-4 text-gray-700 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring focus:ring-blue-300 focus:border-blue-300">
        </div>
    </div>

    @role('Admin')
    <!-- Admin Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Admin</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.dashboard') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Dashboard</h3>
                <p class="text-gray-500 mt-1">Halaman utama admin.</p>
            </a>
            @can('view users')
            <a href="{{ route('admin.users') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Pengguna</h3>
                <p class="text-gray-500 mt-1">Kelola pengguna.</p>
            </a>
            @endcan
            @can('edit users')
            <a href="{{ route('admin.roles') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Role</h3>
                <p class="text-gray-500 mt-1">Kelola role & izin.</p>
            </a>
            @endcan
            @can('edit products')
            <a href="{{ route('admin.pricing') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Harga</h3>
                <p class="text-gray-500 mt-1">Kelola harga jual.</p>
            </a>
            @endcan
            @can('view locations')
            <a href="{{ route('admin.locations') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Lokasi</h3>
                <p class="text-gray-500 mt-1">Kelola lokasi/toko.</p>
            </a>
            @endcan
            @can('view categories')
            <a href="{{ route('admin.categories') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Kategori</h3>
                <p class="text-gray-500 mt-1">Kelola kategori produk.</p>
            </a>
            @endcan
            @can('view units')
            <a href="{{ route('admin.units') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Unit</h3>
                <p class="text-gray-500 mt-1">Kelola satuan produk.</p>
            </a>
            @endcan
        </div>
    </div>
    @endrole

    @canany(['access pos', 'view stock', 'view products', 'view grn'])
    <!-- Kasir Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Kasir</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @can('access pos')
            <a href="{{ route('pos.kasir') }}" class="bg-blue-500 text-white p-6 rounded-lg shadow-lg hover:bg-blue-600 transition-colors duration-300">
                <h3 class="text-xl font-bold">Buka Kasir (POS)</h3>
                <p class="mt-1">Mulai sesi penjualan baru.</p>
            </a>
            @endcan
            @can('view stock')
            <a href="{{ route('pos.stock') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Pengelolaan Stok</h3>
                <p class="text-gray-500 mt-1">Lihat dan kelola stok.</p>
            </a>
            @endcan
            @can('view products')
            <a href="{{ route('pos.products') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Pengelolaan Produk</h3>
                <p class="text-gray-500 mt-1">Lihat dan kelola produk.</p>
            </a>
            @endcan
            @can('view grn')
            <a href="{{ route('pos.grn.index') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Penerimaan Barang</h3>
                <p class="text-gray-500 mt-1">Catat penerimaan barang baru.</p>
            </a>
            @endcan
        </div>
    </div>
    @endcanany

    @canany(['view simpanan', 'view pinjaman', 'view angsuran', 'view cashout'])
    <!-- Koperasi Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Koperasi</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @can('view simpanan')
            <a href="{{ route('koperasi.simpanan') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Simpanan</h3>
                <p class="text-gray-500 mt-1">Kelola simpanan anggota.</p>
            </a>
            @endcan
            @can('view pinjaman')
            <a href="{{ route('koperasi.pinjaman') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Pinjaman</h3>
                <p class="text-gray-500 mt-1">Kelola pinjaman anggota.</p>
            </a>
            @endcan
            @can('view angsuran')
            <a href="{{ route('koperasi.angsuran') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Angsuran</h3>
                <p class="text-gray-500 mt-1">Kelola angsuran pinjaman.</p>
            </a>
            @endcan
            @can('view cashout')
            <a href="{{ route('koperasi.cashout') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800">Cash Out</h3>
                <p class="text-gray-500 mt-1">Kelola penarikan tunai.</p>
            </a>
            @endcan
        </div>
    </div>
    @endcanany
</div>
