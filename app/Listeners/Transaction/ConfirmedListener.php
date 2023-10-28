<?php

namespace App\Listeners\Transaction;

use App\Services\Contracts\AMQPInterface;
use CodePix\Bank\Domain\Events\EventTransactionConfirmed;
use CodePix\Bank\Domain\Events\EventTransactionCreating;

class ConfirmedListener
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
    public function handle(EventTransactionConfirmed $event): void
    {
        $payload = $event->payload();
        $this->AMQP->publish("transaction.confirmation", $payload);
    }
}
