<?php

namespace App\Providers;

use View;
use Dotenv\Dotenv;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        with(new Dotenv(app()->environmentPath(), app()->environmentFile()))->overload();
        with(new LoadConfiguration())->bootstrap(app());

        if(env("APP_DEBUG", false)) Auth::loginUsingId(env('DEBUG_USER'), true);

        /*;
        View::composer('layouts/app', function($view){
            $invitations = Invitation::where("status","=","wait")->get();
            $view->with('invitations', $invitations);
        });*/
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
