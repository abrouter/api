<?php

namespace Modules\AbRouter\Console;

use Illuminate\Console\Command;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Exception;

class FillOwnerIdExperimentUserBranches extends Command
{
    /**
     * @var string
     */
    protected $name = 'abrouter:fill-owner-id-experiment-user-branches';

    /**
     * @var string
     */
    protected $description = 'fill owner id experiment user branches';

    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->info("Hello. I'm your command. I'll fill owner id for experiment_user_branches table \n");

        $this->info("Making query...");
        $experimentBranchUsers = ExperimentBranchUser::query()
            ->where('owner_id', 0)
            ->limit(10000)
            ->get();

        $counter = $experimentBranchUsers->count();
        $this->info("Chunk: " . $counter);

        $i = 0;
        $experimentBranchUsers->each(function(ExperimentBranchUser $experimentBranchUser) use (&$i, $counter) {
            $i ++;
            if (empty($experimentBranchUser->experiment)) {
                return ;
            }
            $experimentBranchUser->owner_id = $experimentBranchUser->experiment->owner_id;
            $this->info("Processing: {$counter} / $i");
        });

        $this->info("Completed!");
    }
}
