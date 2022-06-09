<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Events;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Repositories\BaseRepository;

class UserEventsRepository extends BaseRepository
{
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

    public function getReferrersByOwner(int $owner): Collection
    {
        return $this
            ->query()
            ->select('referrer')
            ->where('owner_id', $owner)
            ->distinct()
            ->get();
    }

    protected function getModel(): Event
    {
        return new Event();
    }
}
