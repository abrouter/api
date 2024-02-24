<?php

namespace Modules\AbRouter\Console;

use Illuminate\Console\Command;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Exception;
use Modules\AbRouter\Services\UsersExperiments\ProcessAllUsersExperimentsService;

class RunUsersExperimentsIndexer extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'abrouter:users-experiments-indexer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @throws Exception
     */
    public function handle()
    {
        /**
         * @var ProcessAllUsersExperimentsService $processAllUsersExperiments
         */
        $processAllUsersExperiments = app()->make(ProcessAllUsersExperimentsService::class);
        $processAllUsersExperiments->processAll();
    }
}
