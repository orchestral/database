<?php

namespace Orchestra\Database\Console\Migrations;

use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class MigrateCommand extends BaseCommand
{
    use Packages;

    /**
     * Create a new migration command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct($migrator);

        $this->specifyParameters();
    }

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
        return [
            ['package', null, InputOption::VALUE_OPTIONAL, 'The package to migrate.', null],
        ];
    }
}
