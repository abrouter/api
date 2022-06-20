<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Illuminate\Http\Request;
use Modules\AbRouter\Http\Resources2\Event\EventSchema;
use Modules\AbRouter\Http\Resources2\Tag\TagScheme;
use Modules\AbRouter\Http\Transformers\Events\EventTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentStatsTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentBranchStatsTransformer;
use Modules\AbRouter\Http\Transformers\Events\AllEventsTransformer;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\AbRouter\Services\Events\EventCreator;
use Modules\AbRouter\Services\Events\SimpleStatsService;
use Modules\AbRouter\Services\Events\ExperimentStatsService;
use Modules\AbRouter\Services\Events\ExperimentBranchStatsService;
use Modules\AbRouter\Services\Events\AllEventsServices;
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
        $event = $this->eventCreator->create($eventDTO);

        return new EventSchema(new SimpleDataProvider($event));
    }
    
    public function showStats(
        Request $request,
        SimpleStatsService $simpleStatsService,
        AuthDecorator $authDecorator
    ) {
        $results = $simpleStatsService->getStats(new StatsQueryDTO(
            $authDecorator->get()->getId(),
            $request->input('filter.tag'),
            $request->input('filter.date_from'),
            $request->input('filter.date_to')
        ));

        return [
            'percentage' => $results->getPercentage(),
            'counters' => array_merge_recursive(
                $results->getIncrementalCounters(),
                $results->getSummarizationCounters()
            ),
            'referrersCounters' => $results->getReferrersCounters(),
            'referrersPercentage' => $results->getReferrersPercentage(),
            'eventCountersWithDate' => $results->getEventCountersWithDate()
        ];
    }

    public function showStatsByExperimentBranch(
        Request $request,
        ExperimentBranchStatsService $experimentBranchStatsService,
        ExperimentBranchStatsTransformer $experimentBranchStatsTransformer
    ) {
        $results = $experimentBranchStatsService
            ->getStatsByExperimentBranch(
                $experimentBranchStatsTransformer->transform($request)
            );
        
        return [
            'percentage' => $results->getPercentage(),
            'counters' => array_merge(
                $results->getIncrementalCounters(),
                $results->getSummarizationCounters()
            )
        ];
    }

    public function showStatsByExperiment(
        Request $request,
        ExperimentStatsService $experimentStatsService,
        ExperimentStatsTransformer $experimentStatsTransformer
    ) {
        $results = $experimentStatsService
            ->getStatsByExperiment(
                $experimentStatsTransformer->transform($request)
            );

        return [
            'experiment' => $results->getExperiments(),
            'percentage' => $results->getPercentage(),
            'counters' => array_merge_recursive(
                $results->getIncrementalCounters(),
                $results->getSummarizationCounters()
            ),
            'eventCountersWithDate' => $results->getEventCountersWithDate()
        ];
    }

    public function showTags(Event $event)
    {
        $owner = $this->authDecorator->get()->getId();

        return new TagScheme(
            new SimpleDataProvider(
                $event
                    ->newQuery()
                    ->select('tag')
                    ->where('owner_id', $owner)
                    ->distinct()
                    ->get()
            )
        );
    }

    public function getAllStatisticsEventsByUserId(
        AllEventsTransformer $transformer,
        AllEventsServices $allEventsServices,
        string $userId
    ) {
        $owner = $this->authDecorator->get()->getId();
        $allEventsDTO = $transformer->transform($owner, $userId);
        $allEvents = $allEventsServices->getAllEventsWithOwnerByRelatedIdOrUserId($allEventsDTO);

        return new EventSchema(new SimpleDataProvider($allEvents));
    }
}
