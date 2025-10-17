<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen bg-white lg:grid lg:grid-cols-2">
    <!-- Kolom Gambar (Kiri) - Muncul di layar besar -->
    <div class="relative flex-col items-center justify-center hidden h-full bg-gray-900 lg:flex">
        <!-- Ganti URL gambar ini dengan gambar ilustrasi Anda -->
        <img src="https://source.unsplash.com/random/1200x900?technology,office" alt="Login Illustration" class="absolute object-cover w-full h-full opacity-40">
        <div class="relative z-10 text-center text-white">
            <h1 class="text-4xl font-bold">Selamat Datang Kembali</h1>
            <p class="mt-4 text-lg">Kelola bisnis Anda dengan lebih efisien.</p>
        </div>
    </div>

    <!-- Kolom Form (Kanan) -->
    <div class="flex items-center justify-center w-full h-full px-4 py-12 bg-gray-50 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            
            <!-- Logo dan Judul -->
            <div>
                <div class="flex justify-center lg:hidden">
                    <div class="flex items-center justify-center w-24 h-24 bg-gray-200 border-2 border-dashed border-gray-300 rounded-lg">
                        <span class="text-base font-medium text-gray-500">Logo</span>
                    </div>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-center text-gray-900">
                    Masuk ke akun Anda
                </h2>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="mt-8 space-y-6">
                <!-- NRP -->
                <div>
                    <x-input-label for="nrp" :value="__('NRP')" />
                    <x-text-input wire:model="form.nrp" id="nrp" name="nrp" type="text" required autofocus autocomplete="nrp"
                                  class="block w-full px-3 py-3 mt-1 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="{{ __('Masukkan NRP Anda') }}" />
                    <x-input-error :messages="$errors->get('form.nrp')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input wire:model="form.password" id="password" name="password" type="password" required autocomplete="current-password"
                                  class="block w-full px-3 py-3 mt-1 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="{{ __('Password') }}" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input wire:model="form.remember" id="remember" name="remember" type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="block ml-2 text-gray-900">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500">
                                {{ __('Forgot your password?') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <x-primary-button class="relative flex justify-center w-full px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md group hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Masuk') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
