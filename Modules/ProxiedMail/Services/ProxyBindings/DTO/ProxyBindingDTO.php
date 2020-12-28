<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ProxyBindings\DTO;

use Modules\Auth\Entities\User\UserEntityDecorator;

class ProxyBindingDTO
{
    /**
     * @var UserEntityDecorator
     */
    private $userEntity;
    /**
     * @var string
     */
    private $proxyAddress;
    /**
     * @var array $realAddress
     */
    private $realAddress;

    public function __construct(UserEntityDecorator $userEntity, string $proxyAddress, array $realAddress)
    {
        $this->userEntity = $userEntity;
        $this->proxyAddress = $proxyAddress;
        $this->realAddress = $realAddress;
    }

    /**
     * @return UserEntityDecorator
     */
    public function getUserEntity(): UserEntityDecorator
    {
        return $this->userEntity;
    }

    /**
     * @return string
     */
    public function getProxyAddress(): string
    {
        return $this->proxyAddress;
    }

    /**
     * @return array
     */
    public function getRealAddresses(): array
    {
        return $this->realAddress;
    }
}
