<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\IpInfo;

use \Illuminate\Contracts\Cache\Factory as CacheFactory;
use Modules\AbRouter\Entities\IpInfoEntity;
use Modules\AbRouter\Transformers\IpInfo\IpInfoCacheKeyTransformer;

class IpInfoCacheRepository
{
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

    public function get(string $ip): ?IpInfoEntity
    {
        $info = $this->cacheStorage->get($this->ipInfoCacheKeyTransformer->format($ip));
        if ($info === null) {
            return null;
        }

        return unserialize($info);
    }
}
