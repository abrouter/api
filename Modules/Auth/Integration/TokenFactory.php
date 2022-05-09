<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

class TokenFactory
{
    /**
     * @var JwtParser
     */
    private $jwtParser;

    /**
     * TokenFactory constructor.
     * @param JwtParser $jwtParser
     */
    public function __construct(JwtParser $jwtParser)
    {
        $this->jwtParser = $jwtParser;
    }

    /**
     * @param string $jwt
     * @return JwtTokenInterface
     */
    public function createTokenFromJwtString(string $jwt): JwtTokenInterface
    {
        return $this->jwtParser->parse($jwt);
    }
}
