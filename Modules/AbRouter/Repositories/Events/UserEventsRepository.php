<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Events;

use Illuminate\Database\Eloquent\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Repositories\BaseRepository;

class UserEventsRepository extends BaseRepository
{
    public function getWithOwnerByTag(int $owner, ?string $tag): Collection
    {
        $query = $this
            ->query()
            ->where('owner_id', $owner);
        
        if (!empty($tag)) {
            $query = $query->where('tag', $tag);
        }
        
        return $query->get();
    }

    protected function getModel()
    {
        return new Event();
    }
}
