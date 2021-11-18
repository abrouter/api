<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AbRouter\Http\Resources\Event\EventResource;
use Modules\AbRouter\Http\Transformers\Events\EventTransformer;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\EventCreator;
use Modules\AbRouter\Repositories\Events\EventsRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Repositories\Tags\TagsRepository;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
use Modules\AbRouter\Services\Events\SimpleStatsService;
use Modules\Auth\Exposable\AuthDecorator;

class StatisticsController
{
    /**
     * @var EventTransformer
     */
    private $eventTransformer;
    
    /**
     * @var EventCreator
     */
    private $eventCreator;
    
    /**
     * @var AuthDecorator
     */
    private $authDecorator;
    
    public function __construct(
        EventTransformer $eventTransformer,
        EventCreator $eventCreator,
        AuthDecorator $authDecorator
    ) {
        $this->eventTransformer = $eventTransformer;
        $this->eventCreator = $eventCreator;
        $this->authDecorator = $authDecorator;
    }
    
    public function create(Request $request)
    {
        $eventDTO = $this->eventTransformer->transform($request);
        if ($eventDTO->getCountryCode() === 'UA') {
            return \response()->json(['status' => 'received']);
        }
        $event = $this->eventCreator->create($eventDTO);

        return new EventResource($event);
    }
    
    public function showStats(
        Request $request,
        SimpleStatsService $simpleStatsService,
        AuthDecorator $authDecorator
    ) {
        $results = $simpleStatsService->getStats(new StatsQueryDTO(
            $authDecorator->get()->getId(),
            $request->input('tag')
        ));
        
        return [
            'percentage' => $results->getPercentage(),
            'counters' => $results->getCounters(),
        ];
    }

    public function showTags(TagsRepository $tagsRepository)
    {
        $userTags = $tagsRepository->getTagsByUser($this->authDecorator->get()->getId());
        
        return $userTags;
    }
}
