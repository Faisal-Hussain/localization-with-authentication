<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Blog\Interfaces\BlogRepositoryInterface;
use App\Repositories\Blog\BlogRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
