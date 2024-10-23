<?php

namespace App\Queries\Post;

use App\Contracts\Query\QueryContract;
use App\Models\Post;
use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PublicPostQuery implements QueryContract
{
    protected QueryBuilder $builder;

    public function __construct()
    {
        $this->builder = QueryBuilder::for(Post::query()->whereNotNull('published_at'))
            ->allowedIncludes([
                'user',
                'category',
                'tags',
                'likes',
                'comments',
            ])
            ->allowedFilters([
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('user_id'),
            ])
            ->allowedSorts([
                'created_at',
            ]);
    }

    public function query(): QueryBuilder
    {
        return $this->builder;
    }

    public function find(string $id): Model
    {
        return $this->builder->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->builder->get();
    }

    public function findMany(array $ids): Collection
    {
        return $this->builder->findMany($ids);
    }

    public function paginate(): mixed
    {
        return $this->builder->paginate();
    }
}