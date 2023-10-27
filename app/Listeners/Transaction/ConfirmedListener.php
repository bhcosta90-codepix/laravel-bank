<?php

namespace App\Listeners\Transaction;

use App\Services\Contracts\AMQPInterface;
use CodePix\System\Domain\Events\EventTransactionConfirmed;

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
        $this->AMQP->publish("{$payload['bank']}.transaction.confirmation", $payload);
    }
}
