<?php

namespace Modules\Admin\Repositories\Base\Output;

use Illuminate\Database\Eloquent\Builder;
use PhpParser\Node\Expr\FuncCall;

class PaginateCollectionOutput implements OutputInterface
{
    private $take;
    private $query;
    public function __construct(int $take = 15)
    {
        $this->take = $take;
    }

    public function loadQuery(Builder $query): OutputInterface
    {
        $this->query = $query;
        return $this;
    }

    public function get()
    {
        return $this->query->paginate($this->take);
    }
}
