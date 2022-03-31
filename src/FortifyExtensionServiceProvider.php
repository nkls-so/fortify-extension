<?php

namespace Nkls\FortifyExtension;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication as FortifyDisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication as FortifyEnableTwoFactorAuthentication;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController as FortifyTwoFactorAuthenticationController;
use Nkls\FortifyExtension\Actions\DisableTwoFactorAuthentication;
use Nkls\FortifyExtension\Actions\EnableTwoFactorAuthentication;
use Nkls\FortifyExtension\Http\Controllers\TwoFactorAuthenticationController;
use Nkls\FortifyExtension\Listeners\SendTOTPNotification;

class FortifyExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fortify-extension.php', 'fortify-extension');

        $this->app->singleton('fortify-extension', function ($app) {
            return new FortifyExtension(auth()->user());
        });

        $this->app->bind(FortifyTwoFactorAuthenticationController::class, TwoFactorAuthenticationController::class);
        $this->app->bind(FortifyEnableTwoFactorAuthentication::class, EnableTwoFactorAuthentication::class);
        $this->app->bind(FortifyDisableTwoFactorAuthentication::class, DisableTwoFactorAuthentication::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'fortify-extension');

        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureListener();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/fortify-extension.php' => config_path('fortify-extension.php'),
            ], 'fortify-extension-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'fortify-extension-migrations');

            $this->publishes([
                __DIR__.'/../lang' => app()->langPath().'/vendor/fortify-extension'
            ], 'fortify-extension-lang');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (FortifyExtension::$registersRoutes) {
            Route::group([
                'namespace' => 'Nkls\FortifyExtension\Http\Controllers',
                'domain' => config('fortify-extension.domain', null),
                'prefix' => config('fortify-extension.prefix'),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
            });
        }
    }

    /**
     * Configure the listener for events
     *
     *
     */
    protected function configureListener()
    {
        Event::listen([
            TwoFactorAuthenticationEnabled::class,
            TwoFactorAuthenticationChallenged::class,
        ], [
            SendTOTPNotification::class,
            'handle',
        ]);
    }
}
