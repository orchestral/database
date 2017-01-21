<?php

namespace Orchestra\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\RollbackCommand as BaseCommand;

class RollbackCommand extends BaseCommand
{
    use Packages;

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations';
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPaths()
    {
        $path = $this->input->getOption('path');

        if (! is_null($realPath = $this->input->getOption('realpath'))) {
            return [$realPath];
        }

        // If the package is in the list of migration paths we received we will put
        // the migrations in that path. Otherwise, we will assume the package is
        // is in the package directories and will place them in that location.
        if (! is_null($package = $this->input->getOption('package'))) {
            is_null($path) && $path = 'resources/migrations';

            return [$this->packagePath.'/'.$package.'/'.$path];
        }

        // First, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if (! is_null($path)) {
            return [$this->laravel->basePath().'/'.$path];
        }

        return array_merge(
            [$this->getMigrationPath()], $this->migrator->paths()
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['realpath', null, InputOption::VALUE_OPTIONAL, 'The absolute path to migration files.', null],
            ['package', null, InputOption::VALUE_OPTIONAL, 'The package to migrate.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to migration files.', null],
        ];

        return array_merge($options, parent::getOptions());
    }
}
