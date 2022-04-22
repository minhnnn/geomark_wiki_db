<?php

namespace Modules\Admin\Repositories\Base;

use Modules\Admin\Repositories\Base\Query\QueryInterface;
use Modules\Admin\Repositories\Base\Output\OutputInterface;
use Modules\Admin\Repositories\Base\Output\ObjectOutputInterface;

interface RepositoryInterface
{
    public function get(QueryInterface $queryBuilder, OutputInterface $output);
    public function find(QueryInterface $queryBuilder, ObjectOutputInterface $output);
}
