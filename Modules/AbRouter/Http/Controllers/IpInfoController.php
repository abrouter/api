<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Controllers;

use Modules\AbRouter\Http\Resources\IpInfo\IpInfoResource;
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

    public function __invoke(string $ip): IpInfoResource
    {
        $ipInfo = $this->ipInfoRepository->get($ip);
        if (empty($ipInfo)) {
            throw new UnprocessableEntityHttpException();
        }

        return new IpInfoResource($ipInfo);
    }
}
