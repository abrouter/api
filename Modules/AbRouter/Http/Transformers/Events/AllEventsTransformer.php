<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Events;

use Modules\AbRouter\Services\Events\DTO\AllEventsDTO;

class AllEventsTransformer
{
    public function transform(int $owner, string $userId): AllEventsDTO
    {
        return new AllEventsDTO(
            $owner,
            $userId
        );
    }
}
