<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AbRouter\Http\Resources\Event\EventResource;
use Modules\AbRouter\Http\Transformers\Events\EventTransformer;
use Modules\AbRouter\Services\Events\EventCreator;

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
    
    public function __construct(EventTransformer $eventTransformer, EventCreator $eventCreator)
    {
        $this->eventTransformer = $eventTransformer;
        $this->eventCreator = $eventCreator;
    }
    
    public function create(Request $request)
    {
        $eventDTO = $this->eventTransformer->transform($request);
        $event = $this->eventCreator->create($eventDTO);
        
        return new EventResource($event);
    }
}
