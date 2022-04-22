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
        $query = $this->query()->where('owner_id', $ownerId);
        if ($events !== null) {
            $query = $query->whereIn('event_id', $events->pluck('id')->toArray());
        }
        
        return $query->get();
    }

    public function getAllWithOwnersByUserId(int $ownerId, string $userId): Collection
    {
        return $this
            ->query()
            ->where([
                ['owner_id', $ownerId],
                ['user_id', $userId]
            ])
            ->distinct()
            ->pluck('related_user_id');
    }

    protected function getModel(): RelatedUser
    {
        return new RelatedUser();
    }
}
