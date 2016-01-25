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
        $cache = $this->app->make('cache.store');

        QueryBuilder::macro('remember', function ($duration, $key = null) use ($cache) {
            return (new CacheDecorator($this, $cache))->remember($duration, $key);
        });

        QueryBuilder::macro('rememberForever', function ($key = null) use ($cache) {
            return (new CacheDecorator($this, $cache))->rememberForever($key);
        });
    }
}
