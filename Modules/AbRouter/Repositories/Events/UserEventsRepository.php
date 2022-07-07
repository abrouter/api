<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Events;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Repositories\BaseRepository;

class UserEventsRepository extends BaseRepository
{
    /**
     * @param int $owner
     * @param string|null $tag
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return Collection
     */
    public function getWithOwnerByTagAndDate(
        int $owner,
        ?string $tag,
        string $dateFrom = null,
        string $dateTo = null
    ): Collection {
        $query = $this
            ->query()
            ->where('owner_id', $owner);
        
        if (!empty($tag)) {
            $query = $query->where('tag', $tag);
        }

        if(!empty($dateFrom) && !empty($dateTo)) {
            $query = $query->select()->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        return $query->get();
    }

    /**
     * @param int $owner
     * @return Collection
     */
    public function getReferrersByOwner(int $owner): Collection
    {
        return $this
            ->query()
            ->select('referrer', 'event')
            ->where('owner_id', $owner)
            ->distinct()
            ->get();
    }

    /**
     * @param string $userId
     * @param int $ownerId
     * @return Event|null
     */
    public function getUserInfoWithUserIdByOwnerId(string $userId, int $ownerId): ?Event
    {
        $model = $this
            ->query()
            ->select('meta', 'created_at')
            ->where('owner_id', $ownerId)
            ->where(function ($query) use ($userId) {
                $query
                    ->where('temporary_user_id', $userId)
                    ->orwhere('user_id', $userId);
            })
            ->first();

        return $model;
    }

    protected function getModel(): Event
    {
        return new Event();
    }
}
