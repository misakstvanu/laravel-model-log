<?php

namespace Misakstvanu\ModelLog;

use Illuminate\Support\ServiceProvider;
use Misakstvanu\ModelLog\Models\ModelLog;

class ModelLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/model-log.php', 'model-log');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_model_logs_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_model_logs_table.php'),
            ], 'model-log-migrations');

            $this->publishes([
                __DIR__.'/../config/model-log.php' => config_path('model-log.php'),
            ], 'model-log-config');
        }

        // Register terminating callback to save all collected logs at the end of the request
        $this->app->terminating(function () {
            ModelLog::saveCollectedLogs();
        });
    }
}