<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ReceivedEmail;

use Illuminate\Events\Dispatcher;
use Modules\ProxiedMail\Events\ReceivedEmail\ReceivedEmailCreated;
use Modules\ProxiedMail\Models\ReceivedEmail;
use Modules\ProxiedMail\Services\ReceivedEmail\DTO\ReceivedEmailDTO;
use Throwable;

class CreatorService
{
    /**
     * @var Dispatcher $dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ReceivedEmailDTO $receivedEmailDTO
     * @return ReceivedEmail
     * @throws Throwable
     */
    public function create(ReceivedEmailDTO $receivedEmailDTO): ReceivedEmail
    {
        $receivedEmail = new ReceivedEmail([
            'is_processed' => false,
            'recipient_email' => $receivedEmailDTO->getRecipientEmail(),
            'sender_email' => json_decode($receivedEmailDTO->getPayload(), true)['sender'],
            'payload' => $receivedEmailDTO->getPayload(),
        ]);

        $receivedEmail->saveOrFail();
        $this->dispatcher->dispatch(new ReceivedEmailCreated($receivedEmail));
        $receivedEmail->refresh();

        return $receivedEmail;
    }
}
