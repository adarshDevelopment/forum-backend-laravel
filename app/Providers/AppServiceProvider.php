<?php

namespace App\Providers;

use App\Models\User;
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
        //
        // Model::preventLazyLoading();    // dont know what this does as of now, FORGOT what it did

        Gate::define('authorize-user', function (Object $classObject) {
            // return true if post/comment user_id is equal to the currently logged in user
            return $classObject->user_id == request()->user()->id;
        });

        // method done by laracast guy, Jeffery Way
        Gate::define('laracast-gates', function (User $user, Object $classObject) {
            return $classObject->user->is($user);
        });
    }
}
