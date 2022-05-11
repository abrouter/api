<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\IpInfo;

use Modules\AbRouter\Entities\IpInfoEntity;
use Modules\AbRouter\Services\IpInfo\IpInfoRequesterService;
use Modules\AbRouter\Transformers\IpInfo\IpStackTransformer;
use Throwable;

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

    public function get(string $ip): ?IpInfoEntity
    {
        try {
            return $this->ipStackTransformer->transform($this->infoRequesterService->getInfoByIp($ip));
        } catch (Throwable $e) {
            return null;
        }
    }
}
