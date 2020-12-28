<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Modules\Auth\Models\User\User;
use Modules\Auth\Repositories\Auth\TokenRepository;

class AuthGuard implements Guard
{
    use GuardHelpers;

    private $request;
    private $client;

    /**
     * OAuthGuard constructor.
     *
     * Creates a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request      $request
     * @param AuthClient $client
     */
    public function __construct(UserProvider $provider, Request $request, AuthClient $client)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->client = $client;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        if (is_null($this->user)) {
            $this->user = $this->provider->retrieveByCredentials($this->getCredentials()) ?? new User();
        }

        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['username'])) {
            return false;
        }

        return (bool)$this->provider->retrieveByCredentials($credentials);
    }

    /**
     * Get user credentials from token.
     *
     * @return array
     */
    public function getCredentials() : array
    {
        try {
            if (!$accessToken = $this->request->bearerToken()) {
                return [];
            }
            $jwtToken = $this->client->parseJwt($accessToken);
            /**
             * @var TokenRepository $tokenRepository
             */
            $tokenRepository = app()->make(TokenRepository::class);
            $oauthAccessToken = $tokenRepository->find($jwtToken->getJti());
            if (empty($oauthAccessToken)) {
                return [];
            }

            return [
                'user_id' => $oauthAccessToken->getAttribute('user_id'),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
