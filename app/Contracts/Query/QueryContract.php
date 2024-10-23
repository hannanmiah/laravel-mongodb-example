<?php

namespace App\Contracts\Query;

use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

interface QueryContract
{
    public function query(): QueryBuilder;

    public function find(string $id): Model;

    public function all(): Collection;

    public function findMany(array $ids): Collection;

    public function paginate(): mixed;


}