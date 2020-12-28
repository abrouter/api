<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ProxyBindings;

use Modules\Core\EntityId\Encoder;
use Modules\ProxiedMail\Models\ProxyBinding;
use Modules\ProxiedMail\Repositories\ProxyBindingRepository;

class DeleterService
{
    /**
     * @var ProxyBindingRepository
     */
    private $proxyBindingRepository;
    /**
     * @var Encoder
     */
    private $encoder;

    public function __construct(ProxyBindingRepository $proxyBindingRepository, Encoder $encoder)
    {
        $this->proxyBindingRepository = $proxyBindingRepository;
        $this->encoder = $encoder;
    }

    public function delete(int $userId, string $proxyBindingId): bool
    {
        $localId = $this->encoder->decode($proxyBindingId, ProxyBinding::getType());
        return $this->proxyBindingRepository->delete($userId, $localId);
    }
}
