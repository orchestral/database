<?php namespace Orchestra\Database;

use Orchestra\Database\Console\Migrations\MigrateCommand;
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
        $this->app->singleton('command.migrate', function ($app) {
            $packagePath = $app->basePath().'/vendor';

            $command = new MigrateCommand($app->make('migrator'));

            return $command->setPackagePath($packagePath);
        });
    }
}
