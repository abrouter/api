<?php
declare(strict_types=1);

namespace Modules\Auth\Entities\User;

use Modules\Auth\Models\User\User;
use Modules\Core\EntityId\ResourceIdInterface;
use Modules\Auth\Entities\AccessToken\AccessToken;

class UserWithAccessToken
{
    private const TYPE = 'oauth-access-tokens';

    /**
     * @var User
     */
    private $user;
    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var bool $isNew
     */
    private bool $isNew;

    public function __construct(User $user, AccessToken $accessToken, bool $isNew)
    {
        $this->user = $user;
        $this->accessToken = $accessToken;
        $this->isNew = $isNew;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function getEntityId(): string
    {
        return $this->accessToken->getEntityId();
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    public static function getType(): string
    {
        return AccessToken::getType();
    }
}
