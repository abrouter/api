<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Validation\UnauthorizedException;
use Modules\Auth\Exposable\AuthDecorator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticatedMiddleware
{
    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function handle($request, Closure $next)
    {
        if (empty($this->authDecorator->get()->getId())) {
            throw new UnauthorizedHttpException('Invalid access token');
        }

        return $next($request);
    }
}
