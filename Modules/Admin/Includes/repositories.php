$this->app->bind(Modules\Admin\Repositories\User\UserRepository::class,
            Modules\Admin\Repositories\User\UserRepositoryEloquent::class);
$this->app->bind(Modules\Admin\Repositories\user\UserRepository::class,
            Modules\Admin\Repositories\user\UserRepositoryEloquent::class);