<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Laravel;
use Modules\Core\EntityId\EntityEncoder;

class Events extends Module implements DependsOnModule
{
    public const TABLE_EVENTS = 'events';
    public const TABLE_RELATED_USERS = 'related_users';

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
            $userId = (new EntityEncoder())->encode(random_int(1, 1000), 'users');
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

    public function createEventsWithUserId(int $owner, array $events)
    {   
        $users = [];

        for ($i = 0; $i < 20; $i++) { 
            $userId = (new EntityEncoder())->encode($i, 'users');
            $users[] = $userId;
        }

        $c = 0;

        for($i = 0, $m = 0, $n = 0; $i < 400; $i++, $m++, $n++) {

            if($m === 20) {
                $m = 0;
            }
            
            if($n === 40) {
                $n = 0;
                $c++;
            }

            $tag = 'test';
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => '',
                'user_id' => $users[$m],
                'event' => $events[$c],
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $recordRelatedUsers = [
                'owner_id' => $owner,
                'event_id' => $eventId,
                'user_id' => $users[$m],
                'related_user_id' => '',
                'created_at' => $createdAt,
            ];
            
            $this->laravel->haveRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
            $this->laravel->seeRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
        }

        return $users;
    }

    public function createEventsWithTemporaryUserId(int $owner, array $events)
    {
        $temporaryUsers = [];

        for ($i = 0; $i < 10; $i++) { 
            $temporaryUserId = substr(md5('user_' . $i), 0, 13);
            $temporaryUsers[] = $temporaryUserId;
        }

        $c = 0;
        
        for($i = 0, $m = 0, $n = 0; $i < 400; $i++, $m++, $n++) {
            
            if($m === 10) {
                $m = 0;
            }

            if($n === 40) {
                $n = 0;
                $c++;
            }

            $tag = 'test';
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => $temporaryUsers[$m],
                'user_id' => '',
                'event' => $events[$c],
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $recordRelatedUsers = [
                'owner_id' => $owner,
                'event_id' => $eventId,
                'user_id' => '',
                'related_user_id' => $temporaryUsers[$m],
                'created_at' => $createdAt,
            ];
            
            $this->laravel->haveRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
            $this->laravel->seeRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
        }

        return $temporaryUsers;
    }

    public function createEventsWithTemporaryUserAndUser(int $owner, array $events)
    {
        $c = 0;

        for($i = 0, $m = 0, $n = 0; $i < 1500; $i++, $m++, $n++) {

            if($m === 100) {
                $m = 0;
            }

            if($n === 150) {
                $n = 0;
                $c++;
            }

            if($m % 2 === 0) {
                $userId = (new EntityEncoder())->encode($m, 'users');
            } else $temporaryUserId = substr(md5('user_' . $m), 0, 13);
            
            $tag = 'test';
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => $temporaryUserId ?? '',
                'user_id' => $userId ?? '',
                'event' => $events[$c],
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $recordRelatedUsers = [
                'owner_id' => $owner,
                'event_id' => $eventId,
                'user_id' => $userId ?? '',
                'related_user_id' => $temporaryUserId ?? '',
                'created_at' => $createdAt,
            ];
            
            $this->laravel->haveRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
            $this->laravel->seeRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);

            unset($userId);
            unset($temporaryUserId);
        }
    }

    public function createEventsWithRelatedUserAndUser(int $owner, array $events)
    {
        $c = 0;

        for($i = 0, $m = 0, $n = 0; $i < 1000; $i++, $m++, $n++) {

            if($m === 150) {
                $m = 0;
            }
            
            if($n === 100) {
                $n = 0;
                $c++;
            }

            if($m % 3 === 0) {
                $userId = (new EntityEncoder())->encode($m, 'users');
            } else $temporaryUserId = substr(md5('user_' . $m), 0, 13);
            
            
            $tag = 'test';
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => $temporaryUserId ?? '',
                'user_id' => $userId ?? '',
                'event' => $events[$c],
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $recordRelatedUsers = [
                'owner_id' => $owner,
                'event_id' => $eventId,
                'user_id' => $userId ?? '',
                'related_user_id' => $temporaryUserId ?? '',
                'created_at' => $createdAt,
            ];
            
            $this->laravel->haveRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
            $this->laravel->seeRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);

            $users[] = $userId ?? $temporaryUserId;
            unset($userId);
            unset($temporaryUserId);
        }
    }

    public function createEventsWithRelatedUserAndUserForExperimentStats(int $owner, array $events)
    {
        $c = 0;

        for($i = 0, $m = 0, $n = 0; $i < 190; $i++, $m++, $n++) {

            if($m === 10) {
                $m = 0;
            }
            
            if($n === 10) {
                $n = 0;
                $c++;

                if($c === 4) {
                    $c = 0;
                }
            }

            if($m % 3 === 0) {
                $userId = (new EntityEncoder())->encode($m, 'users');
            } else $temporaryUserId = substr(md5('user_' . $m), 0, 13);
            
            
            $tag = 'test';
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = '[]';
            $createdAt = (new \DateTime())->format('Y-m-d');
            $updatedAt = (new \DateTime())->format('Y-m-d');

            $recordEvents = [
                'owner_id' => $owner,
                'temporary_user_id' => $temporaryUserId ?? '',
                'user_id' => $userId ?? '',
                'event' => $events[$c],
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt
            ];

            $eventId = $this->laravel->haveRecord(self::TABLE_EVENTS, $recordEvents);
            $this->laravel->seeRecord(self::TABLE_EVENTS, $recordEvents);

            $recordRelatedUsers = [
                'owner_id' => $owner,
                'event_id' => $eventId,
                'user_id' => $userId ?? '',
                'related_user_id' => $temporaryUserId ?? '',
                'created_at' => $createdAt,
            ];
            
            $this->laravel->haveRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);
            $this->laravel->seeRecord(self::TABLE_RELATED_USERS, $recordRelatedUsers);

            $users[] = $userId ?? $temporaryUserId;
            unset($userId);
            unset($temporaryUserId);
        }
        
        return $users;
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
