<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Exception;
use Modules\Auth\Entities\AccessToken\AccessToken;
use Modules\Auth\Entities\AccessToken\UserWithAccessToken;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\Auth\DTO\AuthRequestDTO;

class Authenticator
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
     * @param AuthRequestDTO $authRequestDTO
     * @return UserWithAccessToken
     * @throws Exception
     */
    public function auth(AuthRequestDTO $authRequestDTO): UserWithAccessToken
    {
        $user = $this->userRepository->getOneByUsername($authRequestDTO->getUsername());
        $isMatch = decrypt($user->password) === $authRequestDTO->getPassword();
        if (!$isMatch) {
            throw new Exception('Password mismatch');
        }

        return new UserWithAccessToken($user, new AccessToken($user->createToken('default')));
    }
}
