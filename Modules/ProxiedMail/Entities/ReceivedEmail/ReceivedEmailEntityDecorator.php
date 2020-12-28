<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Entities\ReceivedEmail;

use Illuminate\Support\Collection;
use Modules\ProxiedMail\Models\ReceivedEmail;
use Modules\ProxiedMail\Transformers\EmailAttachments\EmailAttachmentsTransformer;

class ReceivedEmailEntityDecorator
{
    /**
     * @var ReceivedEmail $receivedEmail
     */
    private $receivedEmail;

    /**
     * @var array $parsedPayload
     */
    private $parsedPayload;
    /**
     * @var EmailAttachmentsTransformer
     */
    private $attachmentsTransformer;

    public function __construct(ReceivedEmail $receivedEmail, EmailAttachmentsTransformer $emailAttachmentsTransformer)
    {
        $this->receivedEmail = $receivedEmail;
        $this->parsedPayload = json_decode($receivedEmail->payload, true);
        $this->attachmentsTransformer = $emailAttachmentsTransformer;
    }

    public function getModel()
    {
        return $this->receivedEmail;
    }

    public function getTo(): string
    {
        return (string) $this->getPayloadProperty('To');
    }

    public function getFrom(): string
    {
        return (string) $this->getPayloadProperty('From');
    }

    public function getSubject(): string
    {
        return (string) $this->getPayloadProperty('Subject');
    }

    public function getHeaders(): array
    {
        return (array) $this->getPayloadProperty('message-headers');
    }

    public function getRecipient(): string
    {
        return (string) $this->getPayloadProperty('recipient');
    }

    public function getSender(): string
    {
        return (string) $this->getPayloadProperty('sender');
    }

    public function getBodyHtml(): string
    {
        return (string) $this->getPayloadProperty('body-html');
    }

    public function getAttachments(): Collection
    {
        $attachments = $this->getPayloadProperty('attachments');
        if (empty($attachments)) {
            return collect();
        }
        return $this->attachmentsTransformer->transform(json_decode($attachments, true));
    }

    public function getBodyPlain(): string
    {
        return (string)$this->getPayloadProperty('body-plain');
    }

    public function getParsedPayload(): array
    {
        return $this->parsedPayload;
    }

    private function getPayloadProperty(string $property, $default = null)
    {
        return $this->parsedPayload[$property] ?? $default;
    }
}
