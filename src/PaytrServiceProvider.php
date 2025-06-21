<?php
/**
 * Laravel Paytr
 *
 * @author    Furkan Meclis
 * @copyright 2024 Furkan Meclis
 * @license   MIT
 * @link      https://github.com/furkanmeclis/laravel-paytr
 */

namespace FurkanMeclis\Paytr;

use Illuminate\Support\ServiceProvider;

class PaytrServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../config/paytr.php';
        $this->publishes([$configPath => config_path('paytr.php')], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/paytr.php', 'paytr');
        $this->app->singleton(Payment::class, function ($app) {
            $config = $app->make('config')->get('paytr');

            return new Payment($config['credentials'] ?? [], $config['options'] ?? []);
        });
    }
}