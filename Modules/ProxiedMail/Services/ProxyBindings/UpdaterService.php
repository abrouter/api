<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ProxyBindings;

use Illuminate\Validation\UnauthorizedException;
use Modules\Core\EntityId\Encoder;
use Modules\ProxiedMail\Models\ProxyBinding;
use Modules\ProxiedMail\Repositories\ProxyBindingRepository;
use Modules\ProxiedMail\Repositories\RealAddressesGroupRepository;
use Modules\ProxiedMail\Services\ProxyBindings\DTO\ProxyBindingDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UpdaterService
{
    /**
     * @var ProxyBindingRepository
     */
    private $proxyBindingRepository;
    /**
     * @var Encoder
     */
    private $encoder;
    /**
     * @var RealAddressesGroupRepository
     */
    private $realAddressesGroupRepository;

    public function __construct(
        ProxyBindingRepository $proxyBindingRepository,
        Encoder $encoder,
        RealAddressesGroupRepository $realAddressesGroupRepository
    ) {
        $this->proxyBindingRepository = $proxyBindingRepository;
        $this->encoder = $encoder;
        $this->realAddressesGroupRepository = $realAddressesGroupRepository;
    }

    public function update(int $userId, string $proxyBindingId, ProxyBindingDTO $proxyBindingDTO): ProxyBinding
    {
        $localId = $this->encoder->decode($proxyBindingId, ProxyBinding::getType());
        $proxyBinding = $this->proxyBindingRepository->getOne($localId);
        if ($proxyBinding->user_id !== $userId) {
            throw new UnauthorizedException('Unauthorized');
        }

        if ($proxyBindingDTO->getProxyAddress() !== $proxyBinding->proxy_address) {
            throw new UnprocessableEntityHttpException('Change proxy_address isn\'t allowed');
        }
        if (empty($proxyBindingDTO->getRealAddresses())) {
            throw new UnprocessableEntityHttpException(
                'Records with empty real addresses should be deleted instead of patch'
            );
        }

        $proxyBinding->realAddresses()->delete();
        collect($proxyBindingDTO->getRealAddresses())
            ->each(function (string $realAddress) use ($proxyBinding) {
                $this->realAddressesGroupRepository->create($proxyBinding->id, $realAddress);
            });
        $proxyBinding->refresh();

        return $proxyBinding;
    }
}
