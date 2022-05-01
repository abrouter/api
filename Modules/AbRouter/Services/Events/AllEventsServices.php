<?php

namespace Modules\AbRouter\Services\Events;

use Modules\AbRouter\Services\Events\DTO\AllEventsDTO;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;

class AllEventsServices
{
    /**
     * @var RelatedUserRepository
     */
    private $relatedUserRepository;

    public function __construct(RelatedUserRepository $relatedUserRepository)
    {
        $this->relatedUserRepository = $relatedUserRepository;
    }

    public function getAllEventsWithOwnerByRelatedIdOrUserId(AllEventsDTO $allEventsDTO)
    {
        $allEventsId = $this->relatedUserRepository->getAllEventsIdWithOwnersByRelatedIdOrUserId(
            $allEventsDTO->getOwnerId(),
            $allEventsDTO->getUserId()
        );

        return $allEventsId
            ->load('event')
            ->pluck('event');
    }
}
