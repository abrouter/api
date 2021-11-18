<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\RelatedUser;

use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\AbRouter\Services\RelatedUser\DTO\RelatedUserDTO;

class RelatedUserCreator
{
    public function create(RelatedUserDTO $relatedUserDTO): RelatedUser
    {
        $relatedUser = new RelatedUser();
        $relatedUser->fill([
            'owner_id' => $relatedUserDTO->getOwnerId(),
            'user_id' => $relatedUserDTO->getUserId(),
            'related_user_id' => $relatedUserDTO->getRelatedUserId()
        ]);
        
        $relatedUser->save();
        return $relatedUser;
    }
}
