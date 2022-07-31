<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Users;

use Modules\Auth\Entities\AccessToken\AccessToken;
use Modules\Auth\Entities\User\UserWithAccessToken;
use Modules\Auth\Models\User\User;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\Users\DTO\UserCreateDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class Creator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserCreateDTO $createDTO
     * @return UserWithAccessToken
     * @throws Throwable
     */
    public function create(UserCreateDTO $createDTO): UserWithAccessToken
    {
        if ($this->userRepository->hasUserWithUsername($createDTO->getUsername())) {
            throw new UnprocessableEntityHttpException('User with same username already exists');
        }

        $user = new User([
            'username' => $createDTO->getUsername(),
            'password' => encrypt($createDTO->getPassword()),
        ]);
        $user->saveOrFail();

        $token = new AccessToken($user->createToken('default'));
        return new UserWithAccessToken($user, $token, true);
    }
}
