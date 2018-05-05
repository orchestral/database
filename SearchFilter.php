<?php

namespace Orchestra\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

abstract class SearchFilter
{
    /**
     * Keywords collection.
     *
     * @var array
     */
    protected $keywords = [];

    /**
     * Construct a new Search filter.
     *
     * @param  string  $keyword
     */
    public function __construct(string $keyword)
    {
        $this->keywords = explode(' ', $keyword);
    }

    /**
     * Build search from query builder.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function handle($query)
    {
        $rules = $this->rules();

        foreach ($rules as $keyword => $callback) {
            if (Str::contains($keyword, ':*')) {
                [$tag, ] = explode(':', $keyword, 2);

                $first = array_first($this->keywords, function ($value) use ($tag) {
                    return Str::startsWith($value, "{$tag}:");
                });

                $query->when(! is_null($first), function ($query) use ($callback, $first) {
                    [, $value] = explode(':', $first, 2);

                    call_user_func($callback, $query, $value);

                    return $query;
                });
            } else {
                $query->when(in_array($keyword, $this->keywords), function ($query) use ($callback) {
                    call_user_func($callback, $query);

                    return $query;
                });
            }
        }

        return $query;
    }

    /**
     * Rules definitions.
     *
     * @return array
     */
    abstract protected function rules(): array;
}
