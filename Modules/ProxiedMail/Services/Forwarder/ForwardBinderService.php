<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder;

use Illuminate\Support\Collection;
use Modules\ProxiedMail\Models\ProxyBinding;
use Modules\ProxiedMail\Models\RealAddressesGroups;
use Modules\ProxiedMail\Models\ReceivedEmail;
use Modules\ProxiedMail\Repositories\ProxyBindingRepository;
use Modules\ProxiedMail\Repositories\RealAddressesGroupRepository;
use Modules\ProxiedMail\Repositories\ReceivedEmailsRepository;
use Throwable;
use Modules\ProxiedMail\Services\Forwarder\DTO\BindingCompositeDTO;

class ForwardBinderService
{
    /**
     * @var ProxyBindingRepository
     */
    private $proxyBindingsRepository;
    /**
     * @var RealAddressesGroupRepository
     */
    private $realAddressesGroupRepository;

    /**
     * @var ReceivedEmailsRepository
     */
    private $receivedEmailsRepository;

    /**
     * ForwardBinderService constructor.
     * @param ProxyBindingRepository $proxyBindingRepository
     * @param RealAddressesGroupRepository $realAddressesGroupRepository
     * @param ReceivedEmailsRepository $receivedEmailsRepository
     */
    public function __construct(
        ProxyBindingRepository $proxyBindingRepository,
        RealAddressesGroupRepository $realAddressesGroupRepository,
        ReceivedEmailsRepository $receivedEmailsRepository
    ) {
        $this->proxyBindingsRepository = $proxyBindingRepository;
        $this->realAddressesGroupRepository = $realAddressesGroupRepository;
        $this->receivedEmailsRepository = $receivedEmailsRepository;
    }

    /**
     * @param string $recipient
     * @param string $sender
     * @return BindingCompositeDTO|null
     * @throws Throwable
     */
    public function enforceBinding(string $recipient, string $sender): ?BindingCompositeDTO
    {
        return $this->process($recipient, $sender);
    }

    /**
     * @param string $recipient
     * @param string $sender
     * @return ProxyBinding|null
     * @throws Throwable
     */
    private function process(string $recipient, string $sender): ?BindingCompositeDTO
    {
        $recipientBinding = $this->proxyBindingsRepository->getByProxyAddress($recipient);
        if (empty($recipientBinding)) {
            return null;
        }

        $senderBinding = $this->getSenderBinding($recipientBinding, $sender);

        return new BindingCompositeDTO(
            new BindingBehaviourProxy($recipientBinding),
            new BindingBehaviourProxy($senderBinding)
        );
    }

    /**
     * @param ProxyBinding $proxyBinding
     * @param string $sender
     * @return ProxyBinding|null
     * @throws Throwable
     */
    private function getSenderBinding(ProxyBinding $proxyBinding, string $sender): ?ProxyBinding
    {
        $senderRealAddresses = $this
            ->realAddressesGroupRepository
            ->getByRealAddressWithReverseOrProxied($sender, $proxyBinding->id);
        $senderRealAddress = $senderRealAddresses->count() === 1 ? $senderRealAddresses->first() : null;
        /**
         * @var Collection $senderProxies
         */
        $senderProxies = $senderRealAddresses->reduce(function (Collection $acc, RealAddressesGroups $realAddress) {
            return $acc->push($realAddress->proxyBinding);
        }, collect());

        if ($senderProxies->count() > 1) {
            /**
             * @var RealAddressesGroups $recipientRealAddress
             */
            $recipientRealAddress = $proxyBinding->realAddresses->first();

            $receivedEmails = $this->receivedEmailsRepository->firstByRecipientSender(
                $senderProxies->pluck('proxy_address')->toArray(),
                $recipientRealAddress->real_address
            );
            /**
             * @var ReceivedEmail $receivedEmail
             */
            $receivedEmail = $receivedEmails->first();
            if (!empty($receivedEmail)) {
                /**
                 * @var ProxyBinding $senderProxy
                 */
                $senderProxy = $senderProxies->where('proxy_address', '=', $receivedEmail->recipient_email)->first();
                $senderRealAddress = $senderProxy ? $senderProxy->realAddresses->first() : null;
            }
        }

        $senderProxy = $senderRealAddress ? $senderRealAddress->proxyBinding : null;

        if (!empty($senderProxy)) {
            return $senderProxy;
        }

        $reverseProxy = $this->proxyBindingsRepository->createBinding(
            0,
            $this->generateProxyAddress(),
            [$sender],
            $proxyBinding->id
        );
        $reverseProxy->setRelation('reverseFor', $proxyBinding);

        return $reverseProxy;
    }

    /**
     * @return string
     */
    private function generateProxyAddress(): string
    {
        return md5(uniqid()) . '@proxiedmail.com';
    }
}
