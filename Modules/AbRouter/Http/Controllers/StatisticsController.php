<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AbRouter\Http\Resources\Event\EventResource;
use Modules\AbRouter\Http\Transformers\Events\EventTransformer;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Services\Events\EventCreator;
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
        
        return new EventResource($event);
    }
    
    public function showStats(Request $request, Event $event)
    {
        $events = [
            'visit_mainpage',
            'open_contact_form',
            'visit_form_filler',
            'fill_form_later',
            'form_filler_complete',
            'visited_nutritionists_page',
            'visited_book_call',
            'skip_call_booking',
            'visited_almost_done_page',
            'thankyou_page',
        ];
        $owner = $this->authDecorator->get()->model();
        $allUserEvents = $event
            ->newQuery()
            ->where('owner_id', $owner->id)
            ->get();
        $eventCounters = [];
        $eventPercentage = [];
        $temporaryUserGluesToPersistId = [];
        /**
         * @var Event $allUserEvent
         */
        foreach ($allUserEvents as $allUserEvent) {
            if (empty($allUserEvent->temporary_user_id) || empty($allUserEvent->user_id)) {
                continue;
            }
            
            $temporaryUserGluesToPersistId[$allUserEvent->temporary_user_id] = $allUserEvent->user_id;
        }
        $uniqUsers =[];
        
        /**
         * @var Event $allUserEvent
         */
        foreach ($allUserEvents as $allUserEvent) {
            if (!empty(($temporaryUserGluesToPersistId[$allUserEvent->temporary_user_id]))) {
                $uniqUsers[] = $allUserEvent->user_id;
                continue;
            }
    
            $uniqUsers[] = !empty($allUserEvent->user_id) ? $allUserEvent->user_id
                : $allUserEvent->temporary_user_id;
        }
        $uniqUsers = array_unique($uniqUsers);
        $uniqUsersCount = count($uniqUsers);
        
        foreach ($events as $eventName) {
            if (!isset($eventCounters[$eventName])) {
                $eventCounters[$eventName] = 0;
            }
            
            foreach ($uniqUsers as $uniqUser) {
                $count = $allUserEvent
                    ->where(function ($query) use ($uniqUser) {
                        $query->where('temporary_user_id', $uniqUser);
                        $query->orWhere('user_id', $uniqUser);
                        return $query;
                    })
                    ->where('event', $eventName)
                    ->count();
                
                if ($count) {
                    $eventCounters[$eventName] ++;
                }
            }
        }
    
        foreach ($events as $eventName) {
            $counter = $eventCounters[$eventName];
            
            if ($uniqUsersCount ===0) {
                $eventPercentage[$eventName] = 0;
                continue;
            }
            $eventPercentage[$eventName] = intval(($counter / $uniqUsersCount) * 100);
        }
        
        return [
            'percentage' => $eventPercentage,
            'counters' => $eventCounters,
        ];
    }
}
