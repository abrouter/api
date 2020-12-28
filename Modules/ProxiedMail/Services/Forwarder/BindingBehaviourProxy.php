<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder;

use Illuminate\Support\Collection;
use Modules\ProxiedMail\Models\ProxyBinding;

class BindingBehaviourProxy
{
    /**
     * @var ProxyBinding
     */
    private $proxyBinding;

    public function __construct(ProxyBinding $proxyBinding)
    {
        $this->proxyBinding = $proxyBinding;
    }

    public function getForwardTo(): Collection
    {
        return $this->proxyBinding->realAddresses->pluck('real_address');
    }

    public function getFrom(): string
    {
        return $this->proxyBinding->proxy_address;
    }

    public function getId()
    {
        return $this->proxyBinding->id;
    }

    public function incrementReceivedEmails(): self
    {
        $this->proxyBinding->received_emails ++;
        $this->proxyBinding->save();
        return $this;
    }

    public function isShowOriginalFrom(): bool
    {
        return !empty($this->proxyBinding->reverseFor);
    }
}
