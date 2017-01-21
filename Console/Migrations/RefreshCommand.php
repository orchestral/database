<?php

namespace Orchestra\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\RefreshCommand as BaseCommand;

class RefreshCommand extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $database = $this->input->getOption('database');
        $force    = $this->input->getOption('force');
        $path     = $this->input->getOption('path');
        $realpath = $this->input->getOption('realpath');

        $this->call('migrate:reset', [
            '--database' => $database,
            '--force'    => $force,
        ]);

        // The refresh command is essentially just a brief aggregate of a few other of
        // the migration commands and just provides a convenient wrapper to execute
        // them in succession. We'll also see if we need to re-seed the database.
        $this->call('migrate', [
            '--database' => $database,
            '--force'    => $force,
            '--path'     => $path,
            '--realpath' => $realpath,
        ]);

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['realpath', null, InputOption::VALUE_OPTIONAL, 'The absolute path to migration files.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.'],
        ];
    }
}
