<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Exception;
use Modules\Auth\Entities\AccessToken\AccessToken;
use Modules\Auth\Repositories\User\UserRepository;
use Modules\Auth\Services\Auth\DTO\GooglePayloadDTO;
use Modules\Auth\Entities\User\UserWithAccessToken;
use Modules\Auth\Services\Auth\CreateRandomPassword;
use Modules\Auth\Models\User\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthenticatorWithGoogle
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CreateRandomPassword
     */
    private $password;

    public function __construct(UserRepository $userRepository, CreateRandomPassword $password)
    {
        $this->userRepository = $userRepository;
        $this->password = $password;
    }

    /**
     * @param GooglePayloadDTO $googlePayloadDTO
     * @return UserWithAccessToken
     * @throws AccessDeniedHttpException
     */
    public function authOrCreate(GooglePayloadDTO $googlePayloadDTO): UserWithAccessToken
    {
        $user = $this->userRepository->hasUserWithUsername($googlePayloadDTO->getUsername());

        if (!$user) {
            $user = new User([
                'username' => $googlePayloadDTO->getUsername(),
                'password' => encrypt($this->password->create()),
                'google_id' => $googlePayloadDTO->getGoogleId()
            ]);

            $user->saveOrFail();

        } else $user = $this->userRepository->getOneByUsername($googlePayloadDTO->getUsername());

        return new UserWithAccessToken($user, new AccessToken($user->createToken('default')));
    }
}
