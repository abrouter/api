<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\IpInfo;

use Modules\AbRouter\Entities\IpInfoEntity;
use Modules\AbRouter\Services\IpInfo\IpInfoRequesterService;
use Modules\AbRouter\Transformers\IpInfo\IpStackTransformer;

class IpInfoRepository
{
    /**
     * @var IpInfoRequesterService
     */
    private $infoRequesterService;

    /**
     * @var IpStackTransformer
     */
    private $ipStackTransformer;

    public function __construct(
        IpInfoRequesterService $infoRequesterService,
        IpStackTransformer $ipStackTransformer
    ) {
        $this->infoRequesterService = $infoRequesterService;
        $this->ipStackTransformer = $ipStackTransformer;
    }

    public function getInfoByIp(string $ip): ?IpInfoEntity
    {
        return $this->ipStackTransformer->transform($this->infoRequesterService->getInfoByIp($ip));
    }
}
