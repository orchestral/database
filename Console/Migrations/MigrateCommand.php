<?php namespace Orchestra\Database\Console\Migrations;

use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class MigrateCommand extends BaseCommand
{
    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {

        $path = $this->input->getOption('path');

        // First, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if (! is_null($path)) {
            return $this->laravel['path.base'].'/'.$path;
        }

        return $this->laravel['path.database'].'/migrations';
    }
}
