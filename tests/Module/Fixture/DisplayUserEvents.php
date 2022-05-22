<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Laravel;
use Modules\Core\EntityId\EntityEncoder;

class DisplayUserEvents extends Module implements DependsOnModule
{
    public const TABLE_DISPLAY_USER_EVENTS = 'display_user_events';

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
        $events = [
            'visit_mainpage',
            'open_contact_form',
            'visited_book_call',
            'fill_form_later',
            'form_filler_complete',
            'visited_nutrionists_page',
            'skip_call_booking',
            'thankyou_page',
            'leave',
            'sign up'
        ];

        return $events;
    }

    public function haveUserEventsForExperimentStats()
    {
        $events = [
            'visit_mainpage',
            'open_contact_form',
            'visited_book_call',
            'fill_form_later',
        ];

        return $events;
    }

    public function saveUserEvents(int $owner, array $events)
    {
        $eventsId = [];
        $date = (new \DateTime())->format('Y-m-d');

        foreach($events as $event) {
            $eventId = $this->laravel->haveRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => $event,
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $this->laravel->seeRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => $event,
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $encodeEventId = (new EntityEncoder())->encode($eventId, 'display_user_events');
            $eventsId[] = $encodeEventId;
        }

        return ['events' => $events, 'eventsId' => $eventsId];
    }

    public function haveIncrementalEvents(int $owner)
    {
        $events = ['incremental_event_first', 'incremental_events_second'];
        $date = (new \DateTime())->format('Y-m-d');

        foreach(range(0,1) as $i) {
            $this->laravel->haveRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => $events[$i],
                    'type' => 'incremental',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $this->laravel->seeRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => $events[$i],
                    'type' => 'incremental',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
        }

        return $events;
    }

    public function haveSummarizableEvents(int $owner)
    {
        $date = (new \DateTime())->format('Y-m-d');

        foreach(range(0,1) as $i) {
            $this->laravel->haveRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => 'summarizable_event',
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $this->laravel->seeRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $owner,
                    'event_name' => 'summarizable_event',
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
        }

        return 'summarizable_event';
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
