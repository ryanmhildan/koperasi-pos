<div class="max-w-5xl mx-auto pt-18 pb-8">
    <!-- Welcome Header (Centered) -->
    <div class="mb-10 pt-6 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Hallo, {{ auth()->user()->full_name }}!</h1>
        <p class="mt-2 text-lg text-gray-500">Selamat datang kembali. @if($role) Anda login sebagai <strong>{{ $role }}</strong>@endif</p>
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

    @if(auth()->user()->hasRole('Admin'))
    <!-- Admin Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Admin</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($adminMenu as $item)
                @can($item['permission'])
                    <a href="{{ route($item['route']) }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $item['label'] }}</h3>
                        <p class="text-gray-500 mt-1">{{ $item['description'] }}</p>
                    </a>
                @endcan
            @endforeach
        </div>
    </div>
    @endif

    <!-- Kasir Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Kasir</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($kasirMenu as $item)
                @can($item['permission'])
                    @if($item['featured'] ?? false)
                        <a href="{{ route($item['route']) }}" class="bg-blue-500 text-white p-6 rounded-lg shadow-lg hover:bg-blue-600 transition-colors duration-300">
                            <h3 class="text-xl font-bold">{{ $item['label'] }}</h3>
                            <p class="mt-1">{{ $item['description'] }}</p>
                        </a>
                    @else
                        <a href="{{ route($item['route']) }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $item['label'] }}</h3>
                            <p class="text-gray-500 mt-1">{{ $item['description'] }}</p>
                        </a>
                    @endif
                @endcan
            @endforeach
        </div>
    </div>

    <!-- Koperasi Menu Section -->
    <div class="mb-12 px-6">
        <p class="text-base font-semibold text-gray-600 mb-3">Koperasi</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($koperasiMenu as $item)
                @can($item['permission'])
                    <a href="{{ route($item['route']) }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $item['label'] }}</h3>
                        <p class="text-gray-500 mt-1">{{ $item['description'] }}</p>
                    </a>
                @endcan
            @endforeach
        </div>
    </div>

</div>