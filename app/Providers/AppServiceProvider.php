<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt; // Import the Volt facade
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register a view namespace so Blade/Volt can resolve `layouts::...` names
        // to the components stored in resources/views/components/layouts
        View::addNamespace('layouts', resource_path('views/components/layouts'));
    }
}