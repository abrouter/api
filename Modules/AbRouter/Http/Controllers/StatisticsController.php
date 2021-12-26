<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AbRouter\Http\Resources\Event\EventResource;
use Modules\AbRouter\Http\Resources\Tag\TagCollection;
use Modules\AbRouter\Http\Transformers\Events\EventTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentStatsTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentBranchStatsTransformer;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\EventCreator;
use Modules\AbRouter\Services\Events\SimpleStatsService;
use Modules\AbRouter\Services\Events\ExperimentStatsService;
use Modules\AbRouter\Services\Events\ExperimentBranchStatsService;
use Modules\AbRouter\Repositories\RelatedUser\RelatedUserRepository;
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
            $request->input('filter.tag')
        ));
        
        return [
            'percentage' => $results->getPercentage(),
            'counters' => $results->getCounters(),
        ];
    }

    public function showStatsByExperimentBranch(
        Request $request,
        ExperimentBranchStatsService $experimentBranchStatsService,
        ExperimentBranchStatsTransformer $experimentBranchStatsTransformer
    ) {
        $results = $experimentBranchStatsService->getStatsByExperimentBranch($experimentBranchStatsTransformer->transform($request));
        
        return [
            'percentage' => $results->getPercentage(),
            'counters' => $results->getCounters(),
        ];
    }

    public function showStatsByExperiment(
        Request $request,
        ExperimentStatsService $experimentStatsService,
        ExperimentStatsTransformer $experimentStatsTransformer
    ) {
        $results = $experimentStatsService->getStatsByExperiment($experimentStatsTransformer->transform($request));
        
        return [
            'percentage' => $results->getPercentage(),
            'counters' => $results->getCounters(),
        ];
    }

    public function showTags(Event $event)
    {
        $owner = $this->authDecorator->get()->getId();

        return new TagCollection(
            $event->newQuery()->select('tag')->where('owner_id', $owner)->distinct()->get()
        );
    }
}
