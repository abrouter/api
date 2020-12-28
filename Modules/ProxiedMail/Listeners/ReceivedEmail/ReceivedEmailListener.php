<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Listeners\ReceivedEmail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\ProxiedMail\Events\ReceivedEmail\ReceivedEmailCreated;
use Modules\ProxiedMail\Services\Forwarder\ForwarderService;

class ReceivedEmailListener implements ShouldQueue
{
    /**
     * @var ForwarderService
     */
    private $forwarderService;

    public function __construct(ForwarderService $forwarderService)
    {
        $this->forwarderService = $forwarderService;
    }

    public function handle(ReceivedEmailCreated $event)
    {
        $this->forwarderService->forward($event->getReceivedEmail());
    }
}
