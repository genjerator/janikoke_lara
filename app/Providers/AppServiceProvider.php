<?php

namespace App\Providers;

use App\Domains\Person\Services\PersonFilterService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PersonFilterService::class, function () {
            return new PersonFilterService(request()->all());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
