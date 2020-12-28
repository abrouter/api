<?php
declare(strict_types=1);

namespace Modules\Auth\Entities\AccessToken;

use Exception;
use Lcobucci\JWT\Signature;
use Lcobucci\JWT\Token;
use Modules\Auth\Integration\JwtTokenInterface;
use OutOfBoundsException;

class JwtToken implements JwtTokenInterface
{
    private $tokenBehavior;

    /**
     * JwtToken constructor.
     * @param array $headers
     * @param array $claims
     * @param Signature|null $signature
     * @param array $payload
     */
    public function __construct(
        array $headers = ['alg' => 'none'],
        array $claims = [],
        Signature $signature = null,
        array $payload = ['', '']
    ) {
        $this->tokenBehavior = new Token($headers, $claims, $signature, $payload);
    }

    /**
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function getHeader(string $name) : string
    {
        try {
            return $this->tokenBehavior->getHeader($name);
        } catch (OutOfBoundsException $exception) {
            throw new Exception($name);
        }
    }

    /**
     * Find header
     * @param string $name
     * @return null|string
     */
    public function findHeader(string $name) : ?string
    {
        try {
            return $this->getHeader($name);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Check claim exists
     * @param $name
     * @return bool
     */
    public function hasClaim($name)
    {
        return $this->tokenBehavior->hasClaim($name);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getClaim(string $name)
    {
        try {
            return $this->tokenBehavior->getClaim($name);
        } catch (\OutOfBoundsException $exception) {
            throw new Exception($name);
        }
    }

    /**
     * Find claim
     * @param string $name
     * @return null|string
     */
    public function findClaim(string $name): ?string
    {
        try {
            return $this->getClaim($name);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getJti() : string
    {
        return $this->getClaim('jti');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUsername() : string
    {
        return $this->getClaim('username');
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isExpired() : bool
    {
        if (!$this->tokenBehavior->hasClaim('exp')) {
            throw new Exception('exp');
        }

        return $this->tokenBehavior->isExpired();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->tokenBehavior;
    }
}
