<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder\DTO;

use Modules\ProxiedMail\Services\Forwarder\BindingBehaviourProxy;

class BindingCompositeDTO
{
    /**
     * @var BindingBehaviourProxy
     */
    private $recipient;
    /**
     * @var BindingBehaviourProxy
     */
    private $sender;

    public function __construct(BindingBehaviourProxy $recipient, BindingBehaviourProxy $sender)
    {
        $this->recipient = $recipient;
        $this->sender = $sender;
    }

    public function getRecipient(): BindingBehaviourProxy
    {
        return $this->recipient;
    }

    public function getSender(): BindingBehaviourProxy
    {
        return $this->sender;
    }
}
