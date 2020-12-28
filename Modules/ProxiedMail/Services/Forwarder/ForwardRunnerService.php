<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder;

use Illuminate\Support\Collection;
use Modules\ProxiedMail\Models\ReceivedEmail;
use Modules\ProxiedMail\Repositories\ReceivedEmailRepository;

class ForwardRunnerService
{
    /**
     * @var ReceivedEmailRepository
     */
    private $receivedEmailRepository;

    /**
     * @var ForwarderService
     */
    private $forwarder;

    public function __construct(
        ReceivedEmailRepository $receivedEmailRepository,
        ForwarderService $forwarderService
    ) {
        $this->receivedEmailRepository = $receivedEmailRepository;
        $this->forwarder = $forwarderService;
    }

    public function execute(): Collection
    {
        $emails = $this->receivedEmailRepository->getUnprocessedEmails();
        return $emails->reduce(function (Collection $acc, ReceivedEmail $receivedEmail) {
            return $acc->push($this->forwarder->forward($receivedEmail));
        }, collect());
    }
}
