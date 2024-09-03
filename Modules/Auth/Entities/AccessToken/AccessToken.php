<?php
declare(strict_types=1);

namespace Modules\Auth\Entities\AccessToken;

use Carbon\Carbon;
use Laravel\Passport\PersonalAccessTokenResult;
use Modules\Core\EntityId\ResourceIdInterface;

class AccessToken
{
    /**
     * @var PersonalAccessTokenResult
     */
    private $accessToken;

    private ?string $tokenString = null;

    private ?string $expiresAt = null;

    private ?string $entityId = null;

    public function __construct(
        ?PersonalAccessTokenResult $accessToken,
        ?string $token = null,
        ?string $expiresAt = null,
        ?string $entityId = null
    ) {
        $this->accessToken = $accessToken;
        if ($token) {
            $this->tokenString = $token;
        }
        if ($expiresAt) {
            $this->expiresAt = $expiresAt;
        }
        if ($entityId) {
            $this->entityId = $entityId;
        }
    }

    public function getToken(): string
    {
        return $this->tokenString ?? $this->accessToken->accessToken;
    }

    public function expiresAt(): string
    {
        if ($this->expiresAt) {
            return $this->expiresAt;
        }

        /**
         * @var Carbon $expiresAt
         */
        $expiresAt = $this->accessToken->token->getAttribute('expires_at');
        return $expiresAt->toDateTimeString();
    }

    public function getEntityId(): string
    {
        if ($this->entityId) {
            return $this->entityId;
        }

        return $this->accessToken->token->getAttribute('id');
    }

    public static function getType(): string
    {
        return 'oauth-access-tokens';
    }
}
