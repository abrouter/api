<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\CustomizationEvents;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\CustomizationEvent\DTO\CustomizationEventDTO;
use Modules\Core\EntityId\Encoder;
use Modules\Core\EntityId\EntityIdTrait;

class CustomizationEventTransformer
{
    /**
     * @var Encoder $encoder
     */
    private $encoder;
    
    public function __construct(
        Encoder $encoder
    )   {
        $this->encoder = $encoder;
    }
    
    public function transform(Request $request): CustomizationEventDTO
    {
        return new CustomizationEventDTO(
            $this->encoder->decode($request->input('data.relationships.user.data.id'), 'users'),
            $request->input('data.attributes.event_name'),
            '0'
        );
    }
}
