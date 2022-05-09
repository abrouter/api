<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Managers\IpInfo;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Modules\AbRouter\Transformers\IpInfo\IpInfoCacheKeyTransformer;
use Modules\AbRouter\Transformers\IpInfo\IpStackTransformer;

use Modules\AbRouter\Entities\IpInfoEntity;

class IpInfoCacheManager
{
    private const TTL = 3600 * 24 * 60;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cacheStorage;

    /**
     * @var IpInfoCacheKeyTransformer
     */
    private $ipInfoCacheKeyTransformer;

    public function __construct(
        CacheFactory $cacheFactory,
        IpInfoCacheKeyTransformer $ipInfoCacheKeyTransformer
    ) {
        $this->cacheStorage = $cacheFactory->store();
        $this->ipInfoCacheKeyTransformer = $ipInfoCacheKeyTransformer;
    }

    public function store(IpInfoEntity $ipInfoEntity): void
    {
        $this->cacheStorage->set(
            $this->ipInfoCacheKeyTransformer->format($ipInfoEntity->getIp()),
            serialize($ipInfoEntity)
        );
    }
}
