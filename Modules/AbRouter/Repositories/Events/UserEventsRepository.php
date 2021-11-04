<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Events;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class UserEventsRepository extends BaseRepository
{
    public function getEvents(int $owner, string $tag = null)
    {
        /**
         * @var Event $model
         */

        $model = $this->query()->where('owner_id', $owner);
        
        if($tag !== null) {
            $model->where('tag', $tag);
        }
        
        return $model->get();
    }

    protected function getModel()
    {
        return new Event();
    }
}
