<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Auth\Entities\AccessToken\AccessToken;
use Modules\Auth\Entities\User\UserWithAccessToken;
use Modules\Auth\Models\User\User;
use Modules\Auth\Repositories\Auth\TokenRepository;

class ShortTokenAuthorizer
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    private JwtGetterByUserService $jwtGetterByUserService;

    public function __construct(
        TokenRepository $repository,
        JwtGetterByUserService $jwtGetterByUserService
    ) {
        $this->tokenRepository = $repository;
        $this->jwtGetterByUserService = $jwtGetterByUserService;
    }
    
    public function authorize(?string $token): ?UserWithAccessToken
    {
        
        if ($token === null) {
            return null;
        }
        
        $auth = strtr($token, [
            'Bearer ' => '',
        ]);
        if (strlen($auth) > 100) {
            return null;
        }
    
        $token = $this->tokenRepository->find($token);
        if (empty($token)) {
            return null;
        }
    
        /**
         * @var User $user
         */
        $user = $token->user()->first();
        if (empty($user)) {
            throw new AuthorizationException('Failed to authorize short token');
        }
        
        $bearer = new UserWithAccessToken(
            $user,
            $this->jwtGetterByUserService->getByUser($user, $token),
            false
        );
        return $bearer;
    }
}
