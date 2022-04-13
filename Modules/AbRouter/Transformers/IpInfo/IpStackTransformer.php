<?php
declare(strict_types=1);

namespace Modules\AbRouter\Transformers\IpInfo;

use Modules\AbRouter\Entities\IpInfoEntity;

class IpStackTransformer
{
    public function transform(array $info): ?IpInfoEntity
    {
        if (!isset($info['ip'])) {
            return null;
        }

        return new IpInfoEntity(
            $info['ip'],
            md5($info['ip']),
            $info['country_code'],
            $info['country_name'],
            $info['city']
        );
    }
}
