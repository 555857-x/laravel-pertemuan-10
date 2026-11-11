<?php

namespace App\Providers;

use App\Models\Project;
use App\Observers\ProjectObserver;
use Illuminate\Pagination\Paginator;
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
    }
}
