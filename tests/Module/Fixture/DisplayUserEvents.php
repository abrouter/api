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

    public function haveUserEvents(int $ownerId)
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

        $date = (new \DateTime())->format('Y-m-d');

        foreach($events as $event) {
            $eventId = $this->laravel->haveRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $ownerId,
                    'event_name' => $event,
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $this->laravel->seeRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $ownerId,
                    'event_name' => $event,
                    'type' => 'summarizable',
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            $encodeEventId = (new EntityEncoder())->encode($eventId, 'display_user_events');
            $saveEvents[] = ['id' => $encodeEventId, 'event_name' => $event];
        }

        return $saveEvents;
    }

    public function haveRevenueEvents(int $ownerId, array $events)
    {
        $date = (new \DateTime())->format('Y-m-d');

        foreach($events as $event) {
            $this->laravel->haveRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $ownerId,
                    'event_name' => $event['event_name'],
                    'type' => $event['type'],
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]
            );
            $this->laravel->seeRecord(
                self::TABLE_DISPLAY_USER_EVENTS,
                [
                    'user_id' => $ownerId,
                    'event_name' => $event['event_name'],
                    'type' => $event['type'],
                    'order' => 0,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
        }

        return $events;
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
