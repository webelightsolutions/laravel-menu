<?php
namespace Webelightdev\LaravelMenu;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config
        $this->publishes([__DIR__.'/../config/menu.php' => config_path('menu.php')]);
        // Migration
        $this->publishes([__DIR__.'/../database/migrations' => $this->app->databasePath().'/migrations'], 'migrations');

        include __DIR__.'/routes.php';
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MenuBuilder::class, function () {
            return MenuBuilder::new();
        });

        $this->app->alias(MenuBuilder::class, 'menu');
        $this->app->make('Webelightdev\LaravelMenu\MenuBuilder');
    }
}
