<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Carbon\Carbon;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\Client;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Modules\Auth\Entities\AccessToken\AccessToken as AccessTokenEntity;
use Modules\Auth\Models\User\User;
use Laravel\Passport\Token;

class JwtGetterByUserService
{
    private function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ) {
        $obj =  (new AccessToken($userIdentifier, $scopes, $clientEntity));
        $obj
            ->setExpiryDateTime(new \DateTimeImmutable());
        $obj->setUserIdentifier($userIdentifier);

        return $obj;
    }

    public function getByUser(User $user, ?Token $token = null): AccessTokenEntity
    {
        if (!$token) {
            /**
             * @var Token $token
             */
            $token = $user->tokens()->orderByDesc('id')->first();
            if (!$token) {
                return new AccessTokenEntity($user->createToken('default'));
            }
        }

        /**
         * @var \Laravel\Passport\Client $client
         */
        $client = $token->client()->first();
        $jwt = $this->getNewToken(new Client(
            $client->id,
            $client->name,
            $client->redirect,
            true
        ), [], $user->id);

        $jwt->setPrivateKey(new CryptKey(file_get_contents('/app/storage/oauth-private.key')));
        $jwt->initJwtConfiguration();
        $jwt->setIdentifier($token->id);
        $jwt = (string) $jwt;

        /**
         * @var Carbon $expires
         */
        $expires = $token->getAttribute('expires_at');

        return new AccessTokenEntity(
            null,
            $jwt,
            $expires->toDateTimeString(),
            $token->id
        );
    }
}
