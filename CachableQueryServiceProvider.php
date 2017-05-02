<?php

namespace Orchestra\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class CachableQueryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $app = $this->app;

        QueryBuilder::macro('remember', function ($duration, $key = null) use ($app) {
            return (new CacheDecorator($this, $app->make('cache.store')))->remember($duration, $key);
        });

        QueryBuilder::macro('rememberForever', function ($key = null) use ($app) {
            return (new CacheDecorator($this, $app->make('cache.store')))->rememberForever($key);
        });

        EloquentBuilder::macro('remember', function ($duration, $key = null) use ($app) {
            return (new CacheDecorator($this, $app->make('cache.store')))->remember($duration, $key);
        });

        EloquentBuilder::macro('rememberForever', function ($key = null) use ($app) {
            return (new CacheDecorator($this, $app->make('cache.store')))->rememberForever($key);
        });
    }
}
