<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Exception;
use Modules\Auth\Entities\AccessToken\AccessToken;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\Auth\DTO\AuthRequestDTO;
use Modules\Auth\Entities\User\UserWithAccessToken;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Authenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    private JwtGetterByUserService $jwtGetterByUserService;

    public function __construct(
        UserRepository $userRepository,
        JwtGetterByUserService $jwtGetterByUserService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtGetterByUserService = $jwtGetterByUserService;
    }

    /**
     * @param AuthRequestDTO $authRequestDTO
     * @return UserWithAccessToken
     * @throws AccessDeniedHttpException
     */
    public function auth(AuthRequestDTO $authRequestDTO): UserWithAccessToken
    {
        $user = $this->userRepository->getOneByUsername($authRequestDTO->getUsername());
        $isMatch = decrypt($user->password) === $authRequestDTO->getPassword();
        if (!$isMatch) {
            throw new AccessDeniedHttpException('Password mismatch');
        }

        return new UserWithAccessToken(
            $user,
            $this->jwtGetterByUserService->getByUser($user),
            false
        );
    }
}
