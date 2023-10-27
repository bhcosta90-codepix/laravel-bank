<?php

namespace App\Listeners\Transaction;

use App\Services\Contracts\AMQPInterface;
use CodePix\System\Domain\Events\EventTransactionCreating;

class CreatedListener
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
    public function handle(EventTransactionCreating $event): void
    {
        $payload = $event->payload();
        $this->AMQP->publish("{$payload['bank']}.transaction.creating", $payload);
    }
}
