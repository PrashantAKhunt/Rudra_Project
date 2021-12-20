<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\OfOffline;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
        Schema::defaultStringLength(191);

        view()->composer('*', function($view) {
            //get chat message offline count which are unread
            if(isset(Auth::user()->id)){
            $username = config('constants.CHAT_USER_ADD') + Auth::user()->id;

            $offline_message_count = OfOffline::where('username', $username)->get()->count();

            $view->with('offline_message_count', $offline_message_count);
            }
            else{
            $view->with('offline_message_count', 0);
            }
        });





        $setting_details = Settings::orderBy('id', 'ASC')->get();

        View::share('setting_details', $setting_details);
    }

}
