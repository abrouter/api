<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ProxyBindings;

use Modules\ProxiedMail\Models\ProxyBinding;
use Modules\ProxiedMail\Repositories\ProxyBindingRepository;
use Modules\ProxiedMail\Repositories\RealAddressesGroupRepository;
use Modules\ProxiedMail\Services\ProxyBindings\DTO\ProxyBindingDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreatorService
{
    /**
     * @var ProxyBindingRepository
     */
    private $proxyBindingRepository;

    /**
     * @var RealAddressesGroupRepository
     */
    private $realAddressesGroupRepository;

    public function __construct(
        ProxyBindingRepository $proxyBindingRepository,
        RealAddressesGroupRepository $realAddressesGroupRepository
    ) {
        $this->proxyBindingRepository = $proxyBindingRepository;
        $this->realAddressesGroupRepository = $realAddressesGroupRepository;
    }

    /**
     * @param ProxyBindingDTO $proxyBindingDTO
     * @return ProxyBinding
     * @throws \Throwable
     */
    public function create(ProxyBindingDTO $proxyBindingDTO): ProxyBinding
    {
        $proxyBinding = $this->proxyBindingRepository->getByProxyAddress($proxyBindingDTO->getProxyAddress());
        if (!$proxyBinding) {
            $proxyBinding = $this->proxyBindingRepository->createBinding(
                $proxyBindingDTO->getUserEntity()->getId(),
                $proxyBindingDTO->getProxyAddress(),
                $proxyBindingDTO->getRealAddresses(),
                0
            );
        } else {
            $proxyBinding->realAddresses()->delete();
            collect($proxyBindingDTO->getRealAddresses())->each(function (string $realAddress) use ($proxyBinding) {
                $this->realAddressesGroupRepository->create($proxyBinding->id, $realAddress);
            });
        }

        return $proxyBinding;
    }
}
