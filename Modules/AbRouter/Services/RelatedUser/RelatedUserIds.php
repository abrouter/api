<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\RelatedUser;

use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;

class RelatedUserIds
{
    /**
     * @var RelatedUserRepository
     */
    private $relatedUserRepository;

    public function __construct(
        RelatedUserRepository $relatedUserRepository
    )
    {
        $this->relatedUserRepository = $relatedUserRepository;
    }

    public function getRelatedUserIds(
        int $ownerId,
        array $userId,
        array $ids = []
    ): array {
        $userIds = $this
            ->relatedUserRepository
            ->getAllUserIdsAndRelatedUserIdByOwnerIdAndUserId(
                $ownerId,
                $userId
            );

        $newIds = [];

        foreach ($userIds as $item) {
            if (!empty($item['user_id'])) {
                $newIds[] = $item['user_id'];
            }

            if (!empty($item['related_user_id'])) {
                $newIds[] = $item['related_user_id'];
            }
        }

        $newIds = array_unique($newIds);
        $searchedId = array_diff($newIds, $userId, $ids);
        $allUserIds = array_merge_recursive($ids, $newIds);
        $allUserIds = array_unique($allUserIds);

        if (empty($userId)) {
            return $allUserIds;
        } else {
            return $this->getRelatedUserIds(
                $ownerId,
                $searchedId,
                $allUserIds
            );
        }
    }
}
