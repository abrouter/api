<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class DisplayUserEvents extends Module implements DependsOnModule
{
    public function haveEvents()
    {
        $events = [];

        for ($i = 0; $i < 10; $i++) { 
            $event = 'event_' . uniqid();
            
            $events[] = $event;
        }

        return $events;
    }
    
    /**
     * {@inheritdoc}
     */
    public function _depends()
    {
        
    }
}
