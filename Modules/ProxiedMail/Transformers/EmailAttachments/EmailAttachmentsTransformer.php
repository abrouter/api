<?php
declare(strict_type=1);

namespace Modules\ProxiedMail\Transformers\EmailAttachments;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Modules\ProxiedMail\Entities\ReceivedEmail\AttachmentEntity;

class EmailAttachmentsTransformer
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function transform(array $attachments): Collection
    {
        $collect = collect();
        foreach ($attachments as $attachment) {
            $content = $this->client->get($attachment['url'], [
                'auth' => ['api', config('services.mailgun.secret')],
            ])->getBody()->getContents();

            $collect->push(new AttachmentEntity(
                $attachment['name'],
                $attachment['size'],
                $content,
                $attachment['mime']
            ));
        }

        return $collect;
    }
}
