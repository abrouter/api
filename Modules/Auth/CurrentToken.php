<?php

namespace Modules\AbRouter;

class CurrentToken
{
    private static $token;
    
    /**
     * @return mixed
     */
    public static function getToken()
    {
        return self::$token;
    }
    
    /**
     * @param mixed $token
     */
    public static function setToken($token): void
    {
        self::$token = $token;
    }
}
