<?php

namespace Mirmuxsin\Laralog;

use Illuminate\Support\ServiceProvider;

class LaralogServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/laralog.php' => config_path('laralog.php'),
        ], 'laralog-config');
    }

}