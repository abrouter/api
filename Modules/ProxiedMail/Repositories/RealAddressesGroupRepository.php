<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Repositories;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\ProxiedMail\Models\ProxyBinding;
use Modules\ProxiedMail\Models\RealAddressesGroups;

class RealAddressesGroupRepository extends BaseRepository
{
    public function create(int $bindingId, string $address): RealAddressesGroups
    {
        $model = $this->getModel()->fill([
            'proxy_binding_id' => $bindingId,
            'real_address' => $address,
        ]);

        $model->saveOrFail();

        return $model;
    }

    /**
     * @param string $realAddress
     * @param int $reverseForId
     * @return Collection|null
     */
    public function getByRealAddressWithReverseOrProxied(string $realAddress, int $reverseForId): ?Collection
    {
        $table = $this->getModel()->getTable();

        $collect = $this
            ->query()
            ->join(
                ProxyBinding::getType(),
                ProxyBinding::getType() . '.id',
                '=',
                $table . '.proxy_binding_id'
            )
            ->where($table . '.real_address', '=', $realAddress)
            ->whereIn(ProxyBinding::getType() . '.reverse_for', [$reverseForId, 0])
            ->get();

        return $collect;
    }

    public function isExists(int $bindingId, array $realAddresses): bool
    {
        $collect = $this->query()
            ->where([
                'proxy_binding_id' => $bindingId,
            ])
            ->whereIn('real_address', $realAddresses)
            ->get();

        return $collect->count() !== 0;
    }

    public function deleteByProxyBinding(int $proxyBindingId): bool
    {
        return $this->query()->where(['proxy_binding_id' => $proxyBindingId])->delete() !== 0;
    }

    public function getModel()
    {
        return new RealAddressesGroups();
    }
}
