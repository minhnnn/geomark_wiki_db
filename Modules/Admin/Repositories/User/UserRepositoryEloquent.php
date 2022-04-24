<?php

namespace Modules\Admin\Repositories\User;

use Modules\Admin\Entities\User;
use Modules\Admin\Repositories\Base\BaseRepository;
use Modules\Admin\Repositories\Base\Input\InputDataInterface;
use Modules\Admin\Repositories\Base\Query\QueryInterface;
use Modules\Admin\Repositories\Base\Output\OutputInterface;
use Modules\Admin\Repositories\Base\Output\ObjectOutputInterface;
use Illuminate\Database\Eloquent\Builder;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    public function query(): Builder
    {
        return User::query();
    }

    public function getUser(
        QueryInterface $queryBuilder,
        OutputInterface $output
    )    {
        return $this->get(
            $queryBuilder,
            $output
        );
    }

    public function findUser(QueryInterface $queryBuilder, ObjectOutputInterface $output)
        {
            return $this->find(
                $queryBuilder,
                $output
            );
        }

    public function createUser(InputDataInterface $inputData)
    {
        return $this->query()->create($inputData->getData());
    }

    public function updateUser(InputDataInterface $inputData, QueryInterface $queryBuilder)
    {
        return $queryBuilder->buildQuery($this->query())
            ->update($inputData->getData());
    }

    public function deleteUser(QueryInterface $queryBuilder)
    {
        return $queryBuilder->buildQuery($this->query())->delete();
    }

    public function getEmptyUser()
    {
        return new  User();
    }

    public function loadOldInput($User, \Illuminate\Http\Request $request)
            {
                $fillable = $User->getFillable();
                foreach($fillable as $field){
                    if($request->old($field)) {
                        $User->$field = $request->old($field);
                    }
                }
                return $User;
            }


}
