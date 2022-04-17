<?php
declare(strict_types=1);

namespace Modules\AbRouter\Transformers\IpInfo;

class IpInfoCacheKeyTransformer
{
    private const KEY_FORMAT = 'ip-info-%s';

    public function format(string $ip): string
    {
        return sprintf(self::KEY_FORMAT, $ip);
    }
}
