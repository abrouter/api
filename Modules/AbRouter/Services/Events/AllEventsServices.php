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
        $date = $this->convertDateTime(
            $allEventsDTO->getDateFrom(),
            $allEventsDTO->getDateTo()
        );

        $allEventsId = $this->relatedUserRepository->getAllEventsIdWithOwnersByRelatedIdOrUserId(
            $allEventsDTO->getOwnerId(),
            $allEventsDTO->getUserId(),
            $date['date_from'],
            $date['date_to']
        );

        return $allEventsId;
    }

    protected function convertDateTime($dateFrom = null, $dateTo = null): array
    {
        if(!empty($dateFrom && $dateTo)) {
            $dateFromConverted = \DateTime::createFromFormat('m-d-Y', $dateFrom)->format('Y-m-d');
            $dateToConverted = \DateTime::createFromFormat('m-d-Y', $dateTo)->format('Y-m-d');
            return ['date_from' => $dateFromConverted, 'date_to' => $dateToConverted];
        }

        $dateFrom = (new \DateTime())->format('Y-m-d');
        $dateTo = (new \DateTime($dateFrom))->add(new \DateInterval('P1D'))->format('Y-m-d');

        return ['date_from' => $dateFrom, 'date_to' => $dateTo];
    }
}
