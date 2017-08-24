<?php

namespace Orchestra\Database\Console\Migrations;

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
    public function setPackagePath($packagePath)
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
    protected function getPackageMigrationPaths($package)
    {
        return collect($this->option('path') ?? 'resources/migrations')
                    ->map(function ($path) use ($package) {
                        return $this->packagePath.'/'.$package.'/'.$path;
                    })->all();
    }
}
