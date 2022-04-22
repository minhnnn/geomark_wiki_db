<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Admin\Repositories\Base\Query\BaseQuery;
use Modules\Admin\Repositories\Base\Query\GetById;
use Modules\Admin\Repositories\Base\Output\PaginateCollectionOutput;
use Modules\Admin\Repositories\Base\Output\ObjectOutput;
use Modules\Admin\Repositories\Base\Input\InputDataFromRequest;
use Modules\Admin\Repositories\User\UserRepository;
use Modules\Admin\Http\Requests\User\CreateRequest;
use Modules\Admin\Http\Requests\User\UpdateRequest;

class UserController extends Controller
{

    private UserRepository $userRepo;

    /**
     * UserController constructor.
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index(){
        $query = new BaseQuery();
        $output = new PaginateCollectionOutput(15);
        $allUser = $this->userRepo->getUser($query, $output);

        return Inertia::render('User/Index', compact('allUser'));
    }

    public function create(Request $request){
        $user = $this->userRepo->getEmptyUser();
        $user = $this->userRepo->loadOldInput($user, $request);

        return Inertia::render('User/Create', compact('user'));
    }

    public function store(CreateRequest $request){
        $input = new InputDataFromRequest($request);
        $this->userRepo->createUser($input);

        return redirect()->route('admin.user.index');
    }

    public function edit($userId, Request $request){
        $query = new GetById($userId);
        $output = new ObjectOutput();
        $user = $this->userRepo->findUser($query, $output);
        $user = $this->userRepo->loadOldInput($user, $request);

        return Inertia::render('User/Edit', compact('user'));
    }


    public function update($userId, UpdateRequest $request)
    {
        $input = new InputDataFromRequest($request);
        $query = new GetById($userId);
        $this->userRepo->updateUser($input, $query);

        return redirect()->route('admin.user.index');

    }

    public function destroy($userId){
        $query = new GetById($userId);
        $this->userRepo->deleteUser($query);

        return Redirect::back()->with('success', 'User deleted.');
    }

}
