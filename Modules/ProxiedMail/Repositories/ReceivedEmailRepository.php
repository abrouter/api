<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Repositories;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\ProxiedMail\Models\ReceivedEmail;

class ReceivedEmailRepository extends BaseRepository
{
    public function getUnprocessedEmails(): Collection
    {
        return $this->query()->where('is_processed', 0)->get();
    }

    protected function getModel()
    {
        return new ReceivedEmail();
    }
}
