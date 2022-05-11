<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\IpInfo;

use \Illuminate\Contracts\Cache\Factory as CacheFactory;
use Modules\AbRouter\Entities\IpInfoEntity;
use Modules\AbRouter\Transformers\IpInfo\IpInfoCacheKeyTransformer;
use Throwable;

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
        try {
            $info = $this->cacheStorage->get($this->ipInfoCacheKeyTransformer->format($ip));
        } catch (Throwable $e) {
            return null;
        }

        if ($info === null) {
            return null;
        }

        return unserialize($info);
    }
}
