<?php

namespace Modules\Admin\Repositories\Base;

use Illuminate\Database\Eloquent\Builder;
use Modules\Admin\Repositories\Base\Query\QueryInterface;
use Modules\Admin\Repositories\Base\Output\OutputInterface;
use Modules\Admin\Repositories\Base\Output\ObjectOutputInterface;

abstract class BaseRepository implements RepositoryInterface
{
    abstract public function query(): Builder;

    public function get(QueryInterface $queryBuilder, OutputInterface $output)
    {
        $query = $queryBuilder->buildQuery($this->query());
        return $output->loadQuery($query)->get();
    }

    public function find(QueryInterface $queryBuilder, ObjectOutputInterface $output)
    {
        $query = $queryBuilder->buildQuery($this->query());
        $loadQuery = $output->loadQuery($query);
        return $output->output($loadQuery);
    }
}
