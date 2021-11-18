<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\RelatedUser;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class RelatedUserRepository extends BaseRepository
{
    /**
     * @param int        $ownerId
     * @param Collection $events
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
    
    protected function getModel()
    {
        return new RelatedUser();
    }
}
