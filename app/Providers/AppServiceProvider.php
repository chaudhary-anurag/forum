<?php

namespace App\Providers;
use App\Channel;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if(env('REDIRECT_HTTPS')){
            $url->formatScheme('https');
        }
        Schema::defaultStringLength(191);
        \View::composer('*',function($view)
            {
                $channels=\Cache::rememberForever('channels',function(){
                  return Channel::all();
                });
                $view->with('channels',$channels);
            });
        \Validator::extend('spamfree','App\Rules\SpamFree@passes');  
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(env('REDIRECT_HTTPS')){
            $this->app['request']->server->set('HTTPS',true);
        }
    }
}
