<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\RelatedUser;

use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;

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
     *
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
            ->get()
            ->pluck('event');

        return $collection;
    }

    protected function getModel(): RelatedUser
    {
        return new RelatedUser();
    }
}
