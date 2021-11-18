<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Events;

use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class EventsRepository extends BaseRepository
{
    public function getEventsByUser(int $userId): Collection
    {
        $collection = $this->query()->where('user_id', $userId)->get();
        return $collection;
    }

    protected function getModel()
    {
        return new DisplayUserEvent();
    }
}
