<?php

namespace Orchestra\Database\Console\Migrations;

use Illuminate\Support\Collection;

trait Packages
{
    /**
     * The path to the packages directory (vendor).
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Set package path.
     *
     * @param  string  $packagePath
     *
     * @return $this
     */
    public function setPackagePath(string $packagePath)
    {
        $this->packagePath = $packagePath;

        return $this;
    }

    /**
     * Get the path to the package migration directory.
     *
     * @param  string  $package
     *
     * @return array
     */
    protected function getPackageMigrationPaths(string $package): array
    {
        $packagePath = $this->packagePath;

        return Collection::make($this->option('path') ?: 'database/migrations')
                    ->map(static function ($path) use ($packagePath, $package) {
                        return $packagePath.'/'.$package.'/'.$path;
                    })->all();
    }
}
