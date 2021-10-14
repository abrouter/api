<?php
declare(strict_types = 1);

namespace Modules\Auth\Http\Middleware;

use Illuminate\Http\Request;
use Modules\AbRouter\CurrentToken;
use Modules\Auth\Services\Auth\ShortTokenAuthorizer;
use Symfony\Component\HttpFoundation\HeaderBag;

class SimpleTokenMiddleware
{
    /**
     * @var ShortTokenAuthorizer
     */
    private  $shortTokenAuthorizer;
    
    public function __construct(ShortTokenAuthorizer $shortTokenAuthorizer)
    {
        $this->shortTokenAuthorizer = $shortTokenAuthorizer;
    }
    
    public function handle(Request $request, \Closure $next)
    {
//        $auth = $request->headers->get('Authorization');
//        $auth = strtr($auth, [
//            'Bearer ' => '',
//        ]);
//        if (strlen($auth) > 100) {
//            return $next($request);
//        }
//
//        $bearer = $this->shortTokenAuthorizer->authorize($auth);
//
//        CurrentToken::setToken('Bearer ' . $bearer->getAccessToken()->getToken());
    
        return $next($request);
    }
}
