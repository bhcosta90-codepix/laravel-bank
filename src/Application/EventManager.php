<?php

declare(strict_types=1);

namespace Bank\Application;

use BRCas\CA\Contracts\Event\EventManagerInterface;
use Costa\Entity\Contracts\EventInterface;
use Illuminate\Support\Facades\Log;

class EventManager implements EventManagerInterface
{
    /**
     * @param EventInterface[] $events
     * @return void
     */
    public function dispatch(array $events): void
    {

        foreach ($events as $event) {
            Log::debug("Event: " . get_class($event) . ": " . json_encode(["data" => $event->payload()]));
            event($event);
        }
    }

}
