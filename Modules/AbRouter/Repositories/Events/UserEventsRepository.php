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
        ?string $dateFrom,
        ?string $dateTo
    ): Collection {
        $query = $this
            ->query()
            ->where('owner_id', $owner);
        
        if (!empty($tag)) {
            $query = $query->where('tag', $tag);
        }

        if(!empty($dateFrom) && !empty($dateTo)) {
            $query = $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        return $query->get();
    }

    protected function getModel(): Event
    {
        return new Event();
    }
}
