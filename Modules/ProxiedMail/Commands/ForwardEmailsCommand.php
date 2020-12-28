<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Commands;

use Illuminate\Console\Command;
use Modules\ProxiedMail\Services\Forwarder\ForwardRunnerService;

class ForwardEmailsCommand extends Command
{
    protected $signature = 'manual-forward-emails';


    public function handle(ForwardRunnerService $forwarderService): void
    {
        $forwarderService->execute();
        $this->output->write("\nExecuted!\n");
    }
}
