$this->app->bind(Modules\Admin\Repositories\User\UserRepository::class,
            Modules\Admin\Repositories\User\UserRepositoryEloquent::class);