<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\ProxiedMail\Models\ProxyBinding;

class ProxyBindingRepository extends BaseRepository
{
    /**
     * @var RealAddressesGroupRepository
     */
    private $realAddressesGroupRepository;

    public function __construct(RealAddressesGroupRepository $realAddressesGroupRepository)
    {
        $this->realAddressesGroupRepository = $realAddressesGroupRepository;
    }

    public function allByUserId(int $userId): Collection
    {
        $collection = $this->query()->where('user_id', $userId)->get();
        return $collection;
    }

    /**
     * @param int $proxyBindingId
     * @return ProxyBinding
     */
    public function getOne(int $proxyBindingId): ProxyBinding
    {
        /**
         * @var ProxyBinding $proxyBinding
         */
        $proxyBinding = $this->query()->findOrFail($proxyBindingId);
        return $proxyBinding;
    }

    public function getByProxyAddress(string $proxyAddress): ?ProxyBinding
    {
        /**
         * @var ProxyBinding $model
         */
        $model = $this->query()->where('proxy_address', $proxyAddress)->first();
        return $model;
    }

    public function getByProxyAddressWithReverse(string $proxyAddress, int $reverseId): ?ProxyBinding
    {
        /**
         * @var ProxyBinding $model
         */
        $model = $this->query()
            ->where('proxy_address', $proxyAddress)
            ->where('reverse_for', $reverseId)
            ->first();

        return $model;
    }

    public function createBinding(
        int $userId,
        string $proxyAddresses,
        array $realAddress,
        int $reverseFor
    ): ?ProxyBinding {
        $proxy = $this->getByProxyAddress($proxyAddresses);
        if (empty($proxy)) {
            $proxy = new ProxyBinding(([
                'user_id' => $userId,
                'proxy_address' => $proxyAddresses,
                'reverse_for' => $reverseFor,
                'received_emails' => 0,
            ]));
            $proxy->saveOrFail();
        }
        collect($realAddress)->each(function (string $realAddress) use ($proxy) {
            $this->realAddressesGroupRepository->create($proxy->id, $realAddress);
        });

        return $proxy;
    }

    public function delete(int $userId, int $proxyBindingId): bool
    {
        $this->realAddressesGroupRepository->deleteByProxyBinding($proxyBindingId);
        return !empty($this->query()->where('user_id', $userId)->where('id', $proxyBindingId)->delete());
    }

    /**
     * @return Model|ProxyBinding
     */
    protected function getModel()
    {
        return new ProxyBinding();
    }
}
