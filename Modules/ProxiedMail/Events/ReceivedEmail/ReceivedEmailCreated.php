<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Events\ReceivedEmail;

use Illuminate\Queue\SerializesModels;
use Modules\ProxiedMail\Models\ReceivedEmail;

class ReceivedEmailCreated
{
    use SerializesModels;

    /**
     * @var ReceivedEmail
     */
    private $receivedEmail;

    public function __construct(ReceivedEmail $receivedEmail)
    {
        $this->receivedEmail = $receivedEmail;
    }

    /**
     * @return ReceivedEmail
     */
    public function getReceivedEmail(): ReceivedEmail
    {
        return $this->receivedEmail;
    }
}
