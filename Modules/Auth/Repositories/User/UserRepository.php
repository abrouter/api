<?php
declare(strict_types=1);

namespace Modules\Auth\Repositories\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Auth\Models\User\User;
use Modules\Core\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function getOneByUsername(string $username): User
    {
        /**
         * @var User $model
         */
        $model = $this->query()->where('username', $username)->firstOrFail();
        return $model;
    }

    public function hasUserWithUsername(string $username): bool
    {
        try {
            $this->getOneByUsername($username);
        } catch (ModelNotFoundException $notFoundException) {
            return false;
        }

        return true;
    }

    public function getOneById(int $id)
    {
        /**
         * @var User $model
         */
        $model = $this->query()->where('id', $id)->firstOrFail();
        return $model;
    }

    protected function getModel()
    {
        return new User();
    }
}
