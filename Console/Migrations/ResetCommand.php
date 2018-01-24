<?php

namespace Orchestra\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\ResetCommand as BaseCommand;

class ResetCommand extends BaseCommand
{
    use Packages;

    /**
     * Get the path to the migration directory.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        // If the package is in the list of migration paths we received we will put
        // the migrations in that path. Otherwise, we will assume the package is
        // is in the package directories and will place them in that location.
        if (! is_null($package = $this->option('package'))) {
            return $this->getPackageMigrationPaths($package);
        }

        return parent::getMigrationPaths();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['package', null, InputOption::VALUE_OPTIONAL, 'The package to migrate.', null],
        ];

        return array_merge($options, parent::getOptions());
    }
}
