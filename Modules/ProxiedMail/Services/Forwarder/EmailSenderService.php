<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Services\Forwarder;

use Illuminate\Mail\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Modules\ProxiedMail\Entities\ReceivedEmail\AttachmentEntity;
use Modules\ProxiedMail\Entities\ReceivedEmail\ReceivedEmailEntityDecorator;
use Modules\ProxiedMail\Models\SentEmailsLogs;
use Modules\ProxiedMail\Services\Forwarder\DTO\BindingCompositeDTO;

class EmailSenderService
{
    private const TEMPLATE_HTML = 'proxiedmail::html';
    private const TEMPLATE_TEXT = 'proxiedmail::text';

    /**
     * @param string $forwardTo
     * @param ReceivedEmailEntityDecorator $decoratedReceived
     * @param BindingCompositeDTO $bindingBehaviourComposite
     */
    public function send(
        string $forwardTo,
        ReceivedEmailEntityDecorator $decoratedReceived,
        BindingCompositeDTO $bindingBehaviourComposite
    ) {
        Mail::send(
            [
                self::TEMPLATE_HTML,
                self::TEMPLATE_TEXT,
            ],
            [
                'html' => $decoratedReceived->getBodyHtml(),
                'text' => $decoratedReceived->getBodyPlain(),
            ],
            function (Message $message) use ($decoratedReceived, $bindingBehaviourComposite, $forwardTo) {
                $from = $bindingBehaviourComposite->getSender()->getFrom();

                //logging
                (new SentEmailsLogs([
                    'proxy_binding_id' => $bindingBehaviourComposite->getRecipient()->getId(),
                    'to' => $forwardTo,
                    'from' => $from,
                    'meta' => json_encode([
                        'sender_proxy_id' => $bindingBehaviourComposite->getSender()->getId(),
                        'recipient_proxy_id' => $bindingBehaviourComposite->getRecipient()->getId(),
                    ]),
                ]))->save();

                $message->to($forwardTo);
                $message->from($from);
                $message->replyTo($from);
                $message->subject($this->getSubject($decoratedReceived, $bindingBehaviourComposite->getSender()));

                try {
                    $this->addAttachments($decoratedReceived->getAttachments(), $message);
                } catch (\Throwable $e) {
                    //todo log
                }
            }
        );
    }

    /**
     * @param Collection $attachments
     * @param Message $message
     * @return Message
     */
    private function addAttachments(Collection $attachments, Message $message): Message
    {
        $attachments->each(function (AttachmentEntity $attachment) use (&$message) {
            $message->attachData($attachment->getContent(), $attachment->getName(), [
                'mime' => $attachment->getMime(),
            ]);
        });

        return $message;
    }

    private function getSubject(
        ReceivedEmailEntityDecorator $decoratedReceived,
        BindingBehaviourProxy $sender
    ): string {
        $subject = $decoratedReceived->getSubject();
        if ($sender->isShowOriginalFrom()) {
            $subjectSource = "[From: {$decoratedReceived->getSender()}]";
            if (stripos($subject, $subjectSource) === false) {
                $subject = join(' ', [$subjectSource, $subject]);
            }
        }

        return $subject;
    }
}
