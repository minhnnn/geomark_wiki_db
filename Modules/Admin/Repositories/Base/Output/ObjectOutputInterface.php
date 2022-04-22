<?php

namespace Modules\Admin\Repositories\Base\Output;

use Illuminate\Database\Eloquent\Builder;

interface ObjectOutputInterface
{
    public function loadQuery(Builder $query): Builder;
    public function output(Builder $query): object;
}
