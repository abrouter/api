<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\ReceivedEmail\DTO;

class ReceivedEmailDTO
{
    /**
     * @var string
     */
    private $payload;
    /**
     * @var string
     */
    private $recipientEmail;

    public function __construct(string $payload, string $recipientEmail)
    {
        $this->payload = $payload;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * @return string
     */
    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }
}
