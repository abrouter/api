<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Repositories;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\ProxiedMail\Models\ReceivedEmail;

class ReceivedEmailsRepository extends BaseRepository
{
    public function firstByRecipientSender(array $recipients, string $sender): ?Collection
    {
        $models = $this
            ->query()
            ->where('sender_email', '=', $sender)
            ->whereIn('recipient_email', $recipients)
            ->orderBy('id')
            ->get();

        return $models;
    }

    public function getModel()
    {
        return new ReceivedEmail();
    }
}
