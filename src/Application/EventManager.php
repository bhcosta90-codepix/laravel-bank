<?php

declare(strict_types=1);

namespace Bank\Application;

use BRCas\CA\Contracts\Event\EventManagerInterface;
use Illuminate\Support\Facades\Log;

class EventManager implements EventManagerInterface
{
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            Log::driver('event')->info(get_class($event));
            event($event);
        }
    }

}
