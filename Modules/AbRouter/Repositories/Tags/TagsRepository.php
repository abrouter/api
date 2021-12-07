<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Tags;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\AbRouter\Models\Events\Event;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class TagsRepository extends BaseRepository
{
    public function getTagsByUser(int $ownerId):Collection
    {
        /**
         * @var Collection $collection
         */
        $collection = $this->query()->select('tag', 'owner_id')->where('owner_id', $ownerId)->distinct()->get();
        return $collection;
    }

    protected function getModel()
    {
        return new Event();
    }
}
