<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Services\PSUOAuthProvider;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        // $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
        
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }
        Socialite::extend('psu', function ($app) {
            $config = $app['config']['services.psu'];
            return new PSUOAuthProvider(
                $app['request'], $config['client_id'], $config['client_secret'], $config['redirect']
            );
        });
    }
}
