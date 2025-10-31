<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Koperasi POS') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        
        <main class="pt-20">
            {{ $slot }}
        </main>
    </div>
    
    @livewireScripts
    <livewire:role-form-modal />
    <livewire:user-form-modal />
    <livewire:user-card-modal />
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:success', event => {
                Swal.fire({
                    title: event.title,
                    text: event.text,
                    icon: 'success',
                });
            });

            Livewire.on('swal:error', event => {
                Swal.fire({
                    title: event.title,
                    text: event.text,
                    icon: 'error',
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>