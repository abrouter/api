<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Laravel;
use Modules\Core\EntityId\Encoder;

class DisplayUserEvents extends Module implements DependsOnModule
{
    public const TABLE_DISPLAYY_USER_EVENTS = 'display_user_events';

    /**
     * @var Laravel
     */

    private $laravel;

    public function _inject(Laravel $laravel)
    {
        $this->laravel = $laravel;
    }

    public function haveUserEvents()
    {
        $events = [];

        for ($i = 0; $i < 10; $i++) { 
            $event = 'event_' . uniqid();
            
            $events[] = $event;
        }

        return $events;
    }

    public function saveUserEvents(int $owner, array $events)
    {
        $eventsId = [];
        $date = (new \DateTime())->format('Y-m-d');

        foreach($events as $event) {
            $eventId = $this->laravel->haveRecord(self::TABLE_DISPLAYY_USER_EVENTS, ['user_id' => $owner, 'event_name' => $event, 'order' => 0, 'created_at' => $date, 'updated_at' => $date]);
            $this->laravel->seeRecord(self::TABLE_DISPLAYY_USER_EVENTS, ['user_id' => $owner, 'event_name' => $event, 'order' => 0, 'created_at' => $date, 'updated_at' => $date]);
            $encodeEventId = (new Encoder())->encode($eventId, 'display_user_events');
            $eventsId[] = $encodeEventId;
        }

        return ['events' => $events, 'eventsId' => $eventsId];
    }
    
    /**
     * {@inheritdoc}
     */
    public function _depends(): array
    {
        return [
            Laravel::class => sprintf('%s is mandatory dependency', Laravel::class),
        ];
    }
}
