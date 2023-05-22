<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\RelatedUser;

use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Abrouter\RelatedUsers\Collections\RelatedUsersCollection;

class RelatedUserRepository extends BaseRepository
{
    /**
     * @param int $ownerId
     * @param Collection|null $events
     *
     * @return Collection
     */
    public function getAllWithOwnerByEvents(int $ownerId, ?Collection $events = null): Collection
    {
        /**
         * @var Collection $collection
         */
        $query = $this->query()->where('owner_id', $ownerId);
        if ($events !== null) {
            $query = $query->whereIn('event_id', $events->pluck('id')->toArray());
        }
        
        return $query->get();
    }

    /**
     * @param int $ownerId
     * @param string $userId
     *
     * @return Collection
     */
    public function getAllWithOwnersByUserId(int $ownerId, string $userId): Collection
    {
        /**
         * @var Collection $collection
         */
        $collection = $this
            ->query()
            ->where([
                ['owner_id', $ownerId],
                ['user_id', $userId]
            ])
            ->get()
            ->unique('related_user_id');

        return $collection;
    }

    /**
     * @param int $owner
     * @param string $id
     * @param string $dateFrom
     * @param string $dateTo
     * @return Collection
     */
    public function getAllEventsIdWithOwnersByRelatedIdOrUserId(
        int $owner,
        string $id,
        string $dateFrom,
        string $dateTo
    ): Collection {
        /**
         * @var Collection $collection
         */
        $collection = $this
            ->query()
            ->where('owner_id', $owner)
            ->where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->orWhere('related_user_id', $id);
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('event')
            ->orderByDesc('id')
            ->get()
            ->pluck('event');

        if (empty($collection)) {
            return Event::query()->where('user_id', $id)->where('owner_id', $owner)->get();
        }


        return $collection;
    }

    /**
     * @param int $ownerId
     * @param array $userIds
     * @return array
     */
    public function getAllUserIdsAndRelatedUserIdByOwnerIdAndUserId(
        int $ownerId,
        array $userIds
    ): array {
        $ids = $this
            ->query()
            ->select(['user_id', 'related_user_id'])
            ->where('owner_id', $ownerId)
            ->where(function($query) use ($userIds) {
                $query
                    ->whereIn('user_id', $userIds)
                    ->orWhereIn('related_user_id', $userIds);
            })
            ->distinct()
            ->get()
            ->toArray();

        return $ids;
    }

    public function getAllByOwnerId(int $ownerId): array
    {
        $relatedUsers = $this
            ->query()
            ->select(['user_id', 'related_user_id'])
            ->where('owner_id', $ownerId)
            ->distinct()
            ->get();

        $relatedUsersCollection = new RelatedUsersCollection();

        foreach ($relatedUsers as $relatedUser) {
            if (empty($relatedUser->user_id) || empty($relatedUser->related_user_id)) {
                continue;
            }

            $relatedUsersCollection->append($relatedUser->user_id, $relatedUser->related_user_id);
        }

        return $relatedUsersCollection->getAll();
    }

    protected function getModel(): RelatedUser
    {
        return new RelatedUser();
    }
}
