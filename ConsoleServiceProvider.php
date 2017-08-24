<?php

namespace Orchestra\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Database\Console\Migrations\FreshCommand;
use Orchestra\Database\Console\Migrations\ResetCommand;
use Orchestra\Database\Console\Migrations\MigrateCommand;
use Orchestra\Database\Console\Migrations\RefreshCommand;
use Orchestra\Database\Console\Migrations\RollbackCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register all of the migration commands.
     *
     * @return void
     */
    public function register()
    {
        $commands = ['Migrate', 'Fresh', 'Rollback', 'Reset', 'Refresh'];

        // We'll simply spin through the list of commands that are migration related
        // and register each one of them with an application container. They will
        // be resolved in the Artisan start file and registered on the console.
        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        // Once the commands are registered in the application IoC container we will
        // register them with the Artisan start event so that these are available
        // when the Artisan application actually starts up and is getting used.
        $this->commands(
            'command.migrate',
            'command.migrate.fresh',
            'command.migrate.rollback',
            'command.migrate.reset',
            'command.migrate.refresh'
        );
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerFreshCommand()
    {
        $this->app->singleton('command.migrate.fresh', function () {
            return new FreshCommand();
        });
    }

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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.migrate',
            'command.migrate.rollback',
            'command.migrate.reset',
            'command.migrate.refresh',
        ];
    }
}
