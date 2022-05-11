<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\RelatedUser;

use Illuminate\Support\Collection;
use Modules\AbRouter\Services\RelatedUser\DTO\AllRelatedUsersDTO;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;

class AllRelatedUsersServices
{
    /**
     * @var RelatedUserRepository
     */
    private $relatedUserRepository;

    public function __construct(RelatedUserRepository $relatedUserRepository)
    {
        $this->relatedUserRepository = $relatedUserRepository;
    }

    public function getAllRelatedUsers(AllRelatedUsersDTO $allRelatedUsersDTO): Collection
    {
        return $this
            ->relatedUserRepository
            ->getAllWithOwnersByUserId(
                $allRelatedUsersDTO->getOwnerId(),
                $allRelatedUsersDTO->getUserId()
            );
    }
}
