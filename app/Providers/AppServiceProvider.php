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
        // Auto login as debug user only if migration have been done and app runs in debug mode
        if(Schema::hasTable('users')) if(env("APP_DEBUG", false)) Auth::loginUsingId(env('LOCAL_USER'), true);
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
