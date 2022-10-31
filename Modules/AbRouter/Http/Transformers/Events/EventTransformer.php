<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Events;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Events\DTO\EventDTO;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\EntityEncoder;
use Modules\Core\EntityId\IdTrait;

class EventTransformer
{
    /**
     * @var EntityEncoder
     */
    private $encoder;

    private AuthDecorator $authDecorator;

    public function __construct(EntityEncoder $encoder, AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
        $this->encoder = $encoder;
    }
    
    public function transform(Request $request): EventDTO
    {
        return new EventDTO(
            $this->authDecorator->get()->getId(),
            $request->input('data.attributes.temporary_user_id') ?? '',
            $request->input('data.attributes.user_id') ?? '',
            $request->input('data.attributes.event'),
            $request->input('data.attributes.value') ?? '',
            $request->input('data.attributes.tag') ?? '',
            $request->input('data.attributes.referrer') ?? '',
            (array) $request->input('data.attributes.meta') ?? '',
            $request->input('data.attributes.ip') ?? '',
            $request->input('data.attributes.created_at') === null ? null : $request->input('data.attributes.created_at'),
            $request->input('data.attributes.country_code') ?? ''
        );
    }
}
