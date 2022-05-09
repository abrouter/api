<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\RelatedUser;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\RelatedUser\DTO\RelatedUserDTO;
use Modules\Core\EntityId\EntityEncoder;
use Modules\Core\EntityId\IdTrait;

class RelatedUserTransformer
{
    /**
     * @var EntityEncoder
     */
    private $encoder;
    
    public function __construct(EntityEncoder $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function transform(int $ownerId, Request $request): RelatedUserDTO
    {
        return new RelatedUserDTO(
            $ownerId,
            (string) $request->input('data.attributes.user_id'),
            (string) $request->input('data.attributes.event_id'),
            null
        );
    }
}
