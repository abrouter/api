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

    public function __construct(PersonalAccessTokenResult $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getToken(): string
    {
        return $this->accessToken->accessToken;
    }

    public function expiresAt(): string
    {
        /**
         * @var Carbon $expiresAt
         */
        $expiresAt = $this->accessToken->token->getAttribute('expires_at');
        return $expiresAt->toDateTimeString();
    }

    public function getEntityId(): string
    {
        return $this->accessToken->token->getAttribute('id');
    }

    public static function getType(): string
    {
        return 'oauth-access-tokens';
    }
}
