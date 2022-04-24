<?php

namespace Modules\Admin\Repositories\Base\Query;

use Illuminate\Database\Eloquent\Builder;

class GetById implements QueryInterface
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function buildQuery(Builder $query): Builder
    {
        return $query->where('id',$this->id);
    }

}
