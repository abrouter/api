<?php

namespace Modules\AbRouter\Console;

use Illuminate\Console\Command;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Exception;

class MigrateTemporaryUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'abrouter:migrate-temporary-user-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temporary user migrate to related user table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @throws Exception
     */
    public function handle()
    {
        $events = (new Event())
            ->newQuery()
            ->get();
        
        foreach ($events as $event) {
            /**
             * @var Event $event
             */
            $relatedUser = new RelatedUser;
            $relatedUser->fill([
                'owner_id' => $event->owner_id,
                'related_user_id' => $event->temporary_user_id,
                'user_id' => $event->user_id,
                'event_id' => $event->id,
            ]);

            $save = $relatedUser->save();
            
            if (!$save) {
                throw new Exception('Migrate error');
            }
        }

        $this->info('The command was successful!');
    }
}
