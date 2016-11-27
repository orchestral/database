<?php

namespace Orchestra\Database;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Database\Console\Migrations\ResetCommand;
use Orchestra\Database\Console\Migrations\MigrateCommand;
use Orchestra\Database\Console\Migrations\RefreshCommand;
use Orchestra\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\MigrationServiceProvider as ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register the "migrate" migration command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migrate', function (Application $app) {
            return $this->getCommandWithPackage(new MigrateCommand($app->make('migrator')));
        });
    }

    /**
     * Register the "rollback" migration command.
     *
     * @return void
     */
    protected function registerRollbackCommand()
    {
        $this->app->singleton('command.migrate.rollback', function ($app) {
            return $this->getCommandWithPackage(new RollbackCommand($app->make('migrator')));
        });
    }

    /**
     * Register the "reset" migration command.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('command.migrate.reset', function ($app) {
            return $this->getCommandWithPackage(new ResetCommand($app->make('migrator')));
        });
    }

    /**
     * Register the "refresh" migration command.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->singleton('command.migrate.refresh', function () {
            return new RefreshCommand();
        });
    }

    /**
     * Set package path for command.
     *
     * @param  object  $command
     *
     * @return object
     */
    protected function getCommandWithPackage($command)
    {
        $packagePath = $this->app->basePath().'/vendor';

        return $command->setPackagePath($packagePath);
    }
}
