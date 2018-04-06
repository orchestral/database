<?php

namespace Orchestra\Database;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Cache\Repository;

class CacheDecorator
{
    /**
     * The key that should be used when caching the query.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * The number of minutes to cache the query.
     *
     * @var int
     */
    protected $cacheMinutes;

    /**
     * The Query Builder.
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $repository;

    /**
     * Construct a new decorator.
     *
     * @param \Illuminate\Database\Query\Builder  $query
     * @param \Illuminate\Contracts\Cache\Repository $repository
     */
    public function __construct($query, Repository $repository)
    {
        $this->repository = $repository;
        $this->query = $query;
    }

    /**
     * Indicate that the query results should be cached.
     *
     * @param  \DateTime|int  $minutes
     * @param  string  $key
     *
     * @return $this
     */
    public function remember($minutes, ?string $key = null)
    {
        $this->cacheMinutes = $minutes;
        $this->cacheKey = $key;

        return $this;
    }

    /**
     * Indicate that the query results should be cached forever.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function rememberForever(?string $key = null)
    {
        return $this->remember(-1, $key);
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param  string  $column
     * @param  string|null  $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(string $column, ?string $key = null): Collection
    {
        $results = $this->get(is_null($key) ? [$column] : [$column, $key]);

        // If the columns are qualified with a table or have an alias, we cannot use
        // those directly in the "pluck" operations since the results from the DB
        // are only keyed by the column itself. We'll strip the table out here.
        return $results->pluck(
            $this->stripTableForPluck($column),
            $this->stripTableForPluck($key)
        );
    }

    /**
     * Strip off the table name or alias from a column identifier.
     *
     * @param  string  $column
     *
     * @return string|null
     */
    protected function stripTableForPluck(string $column): ?string
    {
        return is_null($column) ? $column : last(preg_split('~\.| ~', $column));
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array   $columns
     *
     * @return mixed|static
     */
    public function first($columns = ['*'])
    {
        $this->query->take(1);

        $results = $this->get($columns);

        if ($results instanceof Collection) {
            return $results->first();
        }

        return count($results) > 0 ? reset($results) : null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function get($columns = ['*']): Collection
    {
        return ! is_null($this->cacheMinutes)
                    ? $this->getCached($columns)
                    : $this->getFresh($columns);
    }

    /**
     * Execute the query as a cached "select" statement.
     *
     * @param  array  $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCached($columns = ['*']): Collection
    {
        // If the query is requested to be cached, we will cache it using a unique key
        // for this database connection and query statement, including the bindings
        // that are used on this query, providing great convenience when caching.
        list($key, $minutes) = $this->getCacheInfo();

        $cache = $this->getCache();

        $callback = $this->getCacheCallback($columns);

        // If the "minutes" value is less than zero, we will use that as the indicator
        // that the value should be remembered values should be stored indefinitely
        // and if we have minutes we will use the typical remember function here.
        if ($minutes < 0) {
            return collect($cache->rememberForever($key, $callback));
        }

        return collect($cache->remember($key, $minutes, $callback));
    }

    /**
     * Execute the query as a fresh "select" statement.
     *
     * @param  array  $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFresh($columns = ['*']): Collection
    {
        return $this->query->get($columns);
    }

    /**
     * Get a unique cache key for the complete query.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey ?: $this->generateCacheKey();
    }

    /**
     * Generate the unique cache key for the query.
     *
     * @return string
     */
    public function generateCacheKey(): string
    {
        $name = $this->getConnection()->getName();

        return md5($name.$this->toSql().serialize($this->getBindings()));
    }

    /**
     * Get the cache object with tags assigned, if applicable.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCache(): Repository
    {
        return $this->repository;
    }

    /**
     * Get the Closure callback used when caching queries.
     *
     * @param  array  $columns
     *
     * @return \Closure
     */
    protected function getCacheCallback($columns)
    {
        return function () use ($columns) {
            return $this->getFresh($columns)->all();
        };
    }

    /**
     * Get the cache key and cache minutes as an array.
     *
     * @return array
     */
    protected function getCacheInfo(): array
    {
        return [$this->getCacheKey(), $this->cacheMinutes];
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->query->{$method}(...$parameters);
    }
}
