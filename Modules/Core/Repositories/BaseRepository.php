<?php
declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @return Model
     */
    abstract protected function getModel();

    protected function query()
    {
        return $this
            ->getModel()
            ->newQuery();
    }
}
