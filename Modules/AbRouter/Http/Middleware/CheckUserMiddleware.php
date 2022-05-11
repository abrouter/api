<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Middleware;

use Illuminate\Validation\UnauthorizedException;
use Modules\Auth\Exposable\AuthDecorator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckUserMiddleware
{
    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function handle($request)
    {
        if ($this->authDecorator->get()->getId() !== $request) {
            throw new UnauthorizedHttpException('Invalid access token');
        }

        return $request;
    }
}
