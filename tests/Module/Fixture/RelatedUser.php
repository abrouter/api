<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Module\Laravel;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class RelatedUser extends Module implements DependsOnModule
{
    public const TABLES_RELATED_USERS = 'related_users';

    /**
     * @var Laravel
     */

    private $laravel;

    public function _inject(Laravel $laravel)
    {
        $this->laravel = $laravel;
    }

    public function haveRelatedUserIdWithUserId(int $owner)
    {
        $userId = uniqid();

        for ($n = 0; $n < 5; $n++) {
            $recordRelatedUser = [
                'owner_id' => $owner,
                'event_id' => 1,
                'related_user_id' => uniqid(),
                'user_id' => $userId,
                'created_at' => (new \DateTime())->format('Y-m-d')
            ];

            $this->laravel->haveRecord(self::TABLES_RELATED_USERS, $recordRelatedUser);
            $this->laravel->seeRecord(self::TABLES_RELATED_USERS, $recordRelatedUser);
        }

        return $userId;
    }

    public function haveRelatedUserId(int $owner)
    {
        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user1',
            'user_id' => 'user1',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);

        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user2',
            'user_id' => 'user1',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);

        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user3',
            'user_id' => 'user2',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);

        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user1',
            'user_id' => 'user3',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);

        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user4',
            'user_id' => 'user4',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);

        $this->laravel->haveRecord(self::TABLES_RELATED_USERS,[
            'owner_id' => $owner,
            'event_id' => 1,
            'related_user_id' => 'related_user3',
            'user_id' => 'user5',
            'created_at' => (new \DateTime())->format('Y-m-d')
        ]);
    }

    public function _depends(): array
    {
        return [
            Laravel::class => sprintf('%s is mandatory dependency', Laravel::class),
        ];
    }
}
