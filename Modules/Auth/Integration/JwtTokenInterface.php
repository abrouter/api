<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

interface JwtTokenInterface
{
    /**
     * Get header
     * @param string $name Header name
     * @return string
     */
    public function getHeader(string $name) : string;

    /**
     * Get JWT Claim
     * @param $name
     *
     * @return string|object|mixed
     */
    public function getClaim(string $name);

    /**
     * Get token jti
     * @return string
     */
    public function getJti() : string;

    /**
     * Check for token expired
     * @return bool
     */
    public function isExpired() : bool;

    /**
     * Get username claim
     * @return string
     */
    public function getUsername() : string;

    /**
     * Get jwt as string
     * @return string
     */
    public function __toString();
}
