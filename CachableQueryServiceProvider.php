<?php namespace Orchestra\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CachableQueryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        QueryBuilder::macro('remember', function ($duration, $key = null) {
            return (new CacheDecorator($this, $this->app->make('cache.store')))->remember($duration, $key);
        });

        QueryBuilder::macro('rememberForever', function ($key = null) {
            return (new CacheDecorator($this, $this->app->make('cache.store')))->rememberForever($key);
        });
    }
}
