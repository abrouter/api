<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder;

use Modules\ProxiedMail\Entities\ReceivedEmail\ReceivedEmailEntityDecorator;
use Modules\ProxiedMail\Models\ReceivedEmail;
use Modules\ProxiedMail\Repositories\ReceivedEmailRepository;
use Modules\ProxiedMail\Transformers\EmailAttachments\EmailAttachmentsTransformer;
use Throwable;

class ForwarderService
{
    /**
     * @var ReceivedEmailRepository
     */
    private $receivedEmailRepository;

    /**
     * @var ForwardBinderService
     */
    private $forwardBinderService;

    /**
     * @var EmailAttachmentsTransformer $emailAttachmentsTransformer
     */
    private $emailAttachmentsTransformer;
    /**
     * @var EmailSenderService
     */
    private $emailSenderService;

    public function __construct(
        ReceivedEmailRepository $receivedEmailRepository,
        ForwardBinderService $forwardBinderService,
        EmailAttachmentsTransformer $emailAttachmentsTransformer,
        EmailSenderService $emailSenderService
    ) {
        $this->receivedEmailRepository = $receivedEmailRepository;
        $this->forwardBinderService = $forwardBinderService;
        $this->emailAttachmentsTransformer = $emailAttachmentsTransformer;
        $this->emailSenderService = $emailSenderService;
    }

    /**
     * @param ReceivedEmail $receivedEmail
     * @return ReceivedEmail
     * @throws Throwable
     */
    public function forward(ReceivedEmail $receivedEmail): ReceivedEmail
    {
        $decoratedReceived = new ReceivedEmailEntityDecorator($receivedEmail, $this->emailAttachmentsTransformer);
        $bindingBehaviourComposite = $this->forwardBinderService->enforceBinding(
            $decoratedReceived->getRecipient(),
            $decoratedReceived->getSender()
        );

        if (empty($bindingBehaviourComposite)) {
            return $receivedEmail;
        }
        $bindingBehaviourComposite->getRecipient()->incrementReceivedEmails();
        $bindingBehaviourComposite->getRecipient()->getForwardTo()->each(
            function (string $forwardTo) use ($receivedEmail, $decoratedReceived, $bindingBehaviourComposite) {
                $this->emailSenderService->send($forwardTo, $decoratedReceived, $bindingBehaviourComposite);
            }
        );

        $receivedEmail->is_processed = 1;
        $receivedEmail->save();

        return $receivedEmail;
    }
}
