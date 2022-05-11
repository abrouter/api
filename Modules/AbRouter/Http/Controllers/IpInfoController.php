<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Modules\AbRouter\Http\Resources2\IpInfo\IpInfoScheme;
use Modules\AbRouter\Repositories\IpInfo\IpInfoRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class IpInfoController
{
    /**
     * @var IpInfoRepository
     */
    private $ipInfoRepository;

    public function __construct(IpInfoRepository $ipInfoRepository)
    {
        $this->ipInfoRepository = $ipInfoRepository;
    }

    public function __invoke(string $ip): IpInfoScheme
    {
        $ipInfo = $this->ipInfoRepository->get($ip);
        if (empty($ipInfo)) {
            throw new UnprocessableEntityHttpException();
        }

        return new IpInfoScheme(new SimpleDataProvider($ipInfo));
    }
}
