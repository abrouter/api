<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

class AuthClient
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function validateJwt(string $jwt): void
    {
        return ;
    }

    public function parseJwt(string $jwt): JwtTokenInterface
    {
        return $this->tokenFactory->createTokenFromJwtString($jwt);
    }
}
