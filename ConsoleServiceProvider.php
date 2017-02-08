<?php

namespace Orchestra\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Database\Console\Migrations\ResetCommand;
use Orchestra\Database\Console\Migrations\MigrateCommand;
use Orchestra\Database\Console\Migrations\RefreshCommand;
use Orchestra\Database\Console\Migrations\RollbackCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
