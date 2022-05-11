<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\CustomizationEvents;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\CustomizationEvent\DTO\CustomizationEventUpdateDTO;
use Modules\Core\EntityId\EntityEncoder;
use Modules\Core\EntityId\IdTrait;

class CustomizationEventUpdateTransformer
{
    /**
     * @var EntityEncoder $encoder
     */
    private $encoder;
    
    public function __construct(
        EntityEncoder $encoder
    )   {
        $this->encoder = $encoder;
    }
    
    public function transform(Request $request): CustomizationEventUpdateDTO
    {
        return new CustomizationEventUpdateDTO(
            $this->encoder->decode($request->input('data.attributes.id'), 'display_user_events'),
            $this->encoder->decode($request->input('data.relationships.user.data.id'), 'users'),
            $request->input('data.attributes.event_name')
        );
    }
}
