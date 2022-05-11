<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\RelatedUser;

use Modules\AbRouter\Services\RelatedUser\DTO\AllRelatedUsersDTO;

class AllRelatedUsersTransformer
{
    public function transform($ownerId, $id): AllRelatedUsersDTO
    {
        return new AllRelatedUsersDTO(
            $ownerId,
            $id
        );
    }
}
