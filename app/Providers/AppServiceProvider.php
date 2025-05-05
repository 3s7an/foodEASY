<?php

namespace App\Providers;

use App\Console\Commands\UpdateRecipeTimes;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('bookmark-recipe', function ($user, $recipe) {
            return $user !== null && $user->id !== $recipe->user_id;
        });

        Artisan::command('update:recipe-time', UpdateRecipeTimes::class);
    }
}
