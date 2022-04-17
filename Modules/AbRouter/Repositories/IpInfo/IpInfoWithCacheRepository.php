<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\IpInfo;

use Modules\AbRouter\Entities\IpInfoEntity;
use Modules\AbRouter\Managers\IpInfo\IpInfoCacheManager;

class IpInfoWithCacheRepository
{
    /**
     * @var IpInfoRepository
     */
    private $ipInfoRepository;

    /**
     * @var IpInfoCacheRepository
     */
    private $infoCacheRepository;

    /**
     * @var IpInfoCacheManager
     */
    private $ipInfoCacheManager;

    /**
     * @var array
     */
    private $localStorage;

    public function __construct(
        IpInfoRepository $ipInfoRepository,
        IpInfoCacheRepository $infoCacheRepository,
        IpInfoCacheManager $infoCacheManager
    ) {
        $this->ipInfoRepository = $ipInfoRepository;
        $this->infoCacheRepository = $infoCacheRepository;
        $this->ipInfoCacheManager = $infoCacheManager;
    }

    public function get(string $ip): ?IpInfoEntity
    {
        if (isset($this->localStorage[$ip])) {
            return $this->localStorage[$ip];
        }

        $cachedInfo = $this->infoCacheRepository->get($ip);
        if ($cachedInfo !== null) {
            return $cachedInfo;
        }

        $ipInfo = $this->ipInfoRepository->get($ip);
        if ($ipInfo === null) {
            return null;
        }

        $this->localStorage[$ip] = $ipInfo;
        $this->ipInfoCacheManager->store($ipInfo);

        return $ipInfo;
    }
}
