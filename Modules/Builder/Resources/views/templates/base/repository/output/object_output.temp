<?php

namespace Modules\Admin\Repositories\Base\Output;

use Illuminate\Database\Eloquent\Builder;

class ObjectOutput implements ObjectOutputInterface
{

    public function loadQuery(Builder $query): Builder
    {
        return $query;
    }

    public function output(Builder $query): object
    {
        return $query->first();
    }
}
