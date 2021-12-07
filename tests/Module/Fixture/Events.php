<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Laravel;
use Modules\Core\EntityId\Encoder;

class Events extends Module implements DependsOnModule
{
    public const TABLE_EVENTS = 'events';

    /**
     * @var Laravel
     */

    private $laravel;

    public function _inject(Laravel $laravel)
    {
        $this->laravel = $laravel;
    }

    public function haveEvents(int $owner)
    {
        $events = [];
        $tags = [
            'test-tag-first', 
            'test-tag-second', 
            'test-tag-third', 
            'test-tag-fourth', 
            'test-tag-fifth'
        ];

        for ($i = 0; $i < 10; $i++) { 
            $temporaryUserId = substr(md5('user_' . uniqid()), 0, 13);
            $userId = (new Encoder())->encode(random_int(1, 1000), 'users');
            $event = 'event_' . uniqid();
            $tag = $tags[random_int(0,4)];
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => $temporaryUserId,
                'user_id' => $userId,
                'event' => $event,
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $events[] = array_merge($recordEvents, ['eventId' => $eventId]);
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
