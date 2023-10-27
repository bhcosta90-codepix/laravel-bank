<?php

namespace App\Listeners\Transaction;

use App\Services\Contracts\AMQPInterface;
use CodePix\System\Domain\Events\EventTransactionError;

class ErrorListener
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
    public function handle(EventTransactionError $event): void
    {
        $payload = $event->payload();
        $this->AMQP->publish("{$payload['bank']}.transaction.error", $payload);
    }
}
