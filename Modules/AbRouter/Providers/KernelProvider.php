<?php

namespace Modules\AbRouter\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\AbRouter\Services\UsersExperiments\ProcessAllUsersExperimentsService;

class KernelProvider extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            /**
             * @var ProcessAllUsersExperimentsService $processAllUsersExperiments
             */
            $processAllUsersExperiments = app()->make(ProcessAllUsersExperimentsService::class);
            $processAllUsersExperiments->processAll();
        })->everyThirtyMinutes();
    }
}
