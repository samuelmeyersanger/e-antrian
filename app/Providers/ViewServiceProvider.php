<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\PengaturanMonitor;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using a closure based composer...
        View::composer(['layouts.guest', 'layouts.navigation'], function ($view) {
            $pengaturan = PengaturanMonitor::first();
            $view->with('pengaturan', $pengaturan);
        });
    }
}