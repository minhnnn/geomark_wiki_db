<?php

namespace Modules\Admin\Repositories\Base\Query;

use Illuminate\Database\Eloquent\Builder;
use Modules\Admin\Repositories\Base\Query\QueryInterface;

class BaseQuery implements QueryInterface
{
    public function buildQuery(Builder $query): Builder
    {
        return $query;
    }
}
