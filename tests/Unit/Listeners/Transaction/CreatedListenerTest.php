<?php

declare(strict_types=1);

use App\Listeners\Transaction\CreatedListener;
use App\Services\Contracts\AMQPInterface;
use CodePix\Bank\Domain\Events\EventTransactionCreating;

describe("CreatedListener Unit Test", function(){
    test("handle", function(){
        $mockListener = mock(AMQPInterface::class);
        $mockListener->shouldReceive('publish')->times(1);
        $listener = new CreatedListener($mockListener);

        $mockEvent = mock(EventTransactionCreating::class);
        $mockEvent->shouldReceive('payload')->times(1);

        $listener->handle($mockEvent);
    });
});
