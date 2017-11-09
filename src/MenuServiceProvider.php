<?php
namespace Webelightdev\LaravelMenu;

use Illuminate\Support\ServiceProvider;
use Webelightdev\LaravelMenu\Menu;

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
        //$this->publishes([__DIR__.'/../config/menu.php' => config_path('menu.php')]);
        // Migration
        $this->publishes([__DIR__.'/../database/migrations' => $this->app->databasePath().'/migrations'], 'migrations');
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Menu::class, function () {
            return Menu::new();
        });

        $this->app->alias(Menu::class, 'menu');
    }
}
