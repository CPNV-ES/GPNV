<?php

namespace App\Providers;

use View;
use Dotenv\Dotenv;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // refresh environnement variables 
        with(new Dotenv(app()->environmentPath(), app()->environmentFile()))->overload();
        // Auto login as local user only if it has been set and users table exists
        if(env('LOCAL_USER') && Schema::hasTable('users')) Auth::loginUsingId(env('LOCAL_USER'), true);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
