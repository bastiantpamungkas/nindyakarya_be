<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Progres;
use App\Observers\ProgresObserver;

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
        Progres::observe(ProgresObserver::class);
    }
}
