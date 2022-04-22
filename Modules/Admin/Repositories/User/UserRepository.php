<?php

namespace Modules\Admin\Repositories\User;

use Modules\Admin\Repositories\Base\Input\InputDataInterface;
use Modules\Admin\Repositories\Base\Output\ObjectOutputInterface;
use Modules\Admin\Repositories\Base\Query\QueryInterface;
use Modules\Admin\Repositories\Base\Output\OutputInterface;

interface UserRepository
{

    public function getUser(
        QueryInterface $queryBuilder,
        OutputInterface $output
    );

    public function findUser(
        QueryInterface $queryBuilder,
        ObjectOutputInterface $output
    );

    public function createUser(InputDataInterface $inputData);

    public function updateUser(
        InputDataInterface $inputData,
        QueryInterface $queryBuilder
    );

    public function deleteUser(QueryInterface $queryBuilder);
//
//    public function findUserById($userId);
//
//    public function updateUserById($userId, $all);
//
//    public function deleteUserById($userId);
//
    public function getEmptyUser();
//
    public function loadOldInput($user, \Illuminate\Http\Request $request);
}
