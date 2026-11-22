<?php

namespace App\Providers;

use App\Models\Project;
use App\Observers\ProjectObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Tombol pagination memakai tampilan Bootstrap 5.
        Paginator::useBootstrapFive();

        // Daftarkan observer untuk model Project.
        Project::observe(ProjectObserver::class);

        // P12: named rate limiter. Tiap nama punya counter sendiri di cache,
        // jadi jatah API dan jatah form contact tidak saling mengurangi.
        // ->by() menentukan siapa yang dihitung, di sini per alamat IP.
        RateLimiter::for('portfolio-api', fn (Request $r) =>
            Limit::perMinute(60)->by($r->ip()));

        RateLimiter::for('contact', fn (Request $r) =>
            Limit::perMinute(3)->by($r->ip()));
    }
}
