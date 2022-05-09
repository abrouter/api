<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

use Illuminate\Contracts\Container\BindingResolutionException;
use Modules\Auth\Models\User\User;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Auth\EloquentUserProvider;
use Modules\Auth\Repositories\User\UserRepository;
use Throwable;

class UserProvider extends EloquentUserProvider
{
    /**
     * UserProvider constructor.
     *
     * @param HasherContract $hasher
     * @param string $model
     */
    public function __construct(HasherContract $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * @param array $credentials
     *
     * @return User|null
     * l
     * @throws BindingResolutionException
     *
     * @throws Throwable
     */
    public function retrieveByCredentials(array $credentials): ?User
    {
        if (!$userId = $credentials['user_id'] ?? null) {
            return null;
        }
        $userId = (int) $userId;

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = app()->make(UserRepository::class);
        return $userRepository->getOneById($userId);
    }
}
