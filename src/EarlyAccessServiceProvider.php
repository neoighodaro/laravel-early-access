<?php

namespace Neo\EarlyAccess;

use Neo\EarlyAccess\Events;
use Neo\EarlyAccess\Listeners;
use Neo\EarlyAccess\Console\Commands;
use Illuminate\Support\ServiceProvider;
use Neo\EarlyAccess\SubscriptionServices\DatabaseService;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider;
use Neo\EarlyAccess\SubscriptionServices\Repositories\Database\EloquentRepository;

class EarlyAccessServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'early-access');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'early-access');

        $this->app['router']->namespace('Neo\\EarlyAccess\\Http\\Controllers')
            ->middleware(['web'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        $this->registerEventListeners();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/early-access.php', 'early-access');

        $this->app->singleton('early-access', function ($app) {
            return new EarlyAccess($app['filesystem.disk'], $app['auth.driver']);
        });

        $this->app->bind('early-access.database', function () {
            return new DatabaseService(new EloquentRepository);
        });

        $this->app->bind(SubscriptionProvider::class, function () {
            return app('early-access.' . config('early-access.service'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['early-access'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        $this->publishes([
            __DIR__ . '/../config/early-access.php' => config_path('early-access.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/neo'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/neo'),
        ], 'assets');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/neo'),
        ], 'translations');

        $this->commands([
            Commands\EarlyAccess::class,
        ]);
    }

    /**
     * Register the packages event listeners.
     */
    protected function registerEventListeners()
    {
        $this->app['events']->listen(
            Events\UserSubscribed::class,
            Listeners\SendSubscriptionNotification::class
        );

        $this->app['events']->listen(
            Events\UserUnsubscribed::class,
            Listeners\SendUnsubscribeNotification::class
        );
    }
}
