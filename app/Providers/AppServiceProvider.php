<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Livewire\Component;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // Global Livewire middleware for auth check
        Component::macro('authorize', function ($ability, $arguments = []) {
            abort_unless(auth()->user()->can($ability, $arguments), 403);
        });
    }
}