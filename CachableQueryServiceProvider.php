<?php

namespace Orchestra\Database;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

class CachableQueryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        QueryBuilder::macro('remember', function ($duration, $key = null) {
            return (new CacheDecorator($this, \app('cache.store')))->remember($duration, $key);
        });

        QueryBuilder::macro('rememberForever', function ($key = null) {
            return (new CacheDecorator($this, \app('cache.store')))->rememberForever($key);
        });

        EloquentBuilder::macro('remember', function ($duration, $key = null) {
            return (new CacheDecorator($this, \app('cache.store')))->remember($duration, $key);
        });

        EloquentBuilder::macro('rememberForever', function ($key = null) {
            return (new CacheDecorator($this, \app('cache.store')))->rememberForever($key);
        });
    }
}
