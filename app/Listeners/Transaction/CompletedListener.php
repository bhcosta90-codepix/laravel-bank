<?php

namespace App\Listeners\Transaction;

use App\Services\Contracts\AMQPInterface;
use CodePix\Bank\Domain\Events\EventTransactionCompleted;

class CompletedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AMQPInterface $AMQP)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventTransactionCompleted $event): void
    {
        $payload = $event->payload();
        $this->AMQP->publish("transaction.complete", $payload);
    }
}
