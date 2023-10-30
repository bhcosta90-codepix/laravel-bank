<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\AMQPInterface;
use App\Services\Contracts\RabbitMQInterface;
use Bschmitt\Amqp\Facades\Amqp;
use Closure;
use Illuminate\Support\Facades\Log;
use Throwable;

class RabbitMQService implements AMQPInterface, RabbitMQInterface
{
    public function __construct()
    {
        //
    }

    public function publish($name, array $value = []): void
    {
        Log::debug("Success publish {$name}: " . json_encode($value));
        Amqp::publish($name, json_encode($value + ['bank' => config('system.bank')]));
    }

    public function consume(string $queue, array|string $topic, Closure $closure, array $custom = []): void
    {
        $queue = config('system.bank') . "_" . $queue;

        Log::debug("Starter consumer {$queue}");

        if (is_string($topic)) {
            $topic = [$topic];
        }

        $topics = array_map(function ($value) {
            return config('system.bank') . "." . $value;
        }, $topic);

        $topic = [
            'routing' => $topics,
            'queue_force_declare' => true,
        ];

        do {
            Amqp::consume($queue, function ($message, $resolver) use ($queue, $closure) {
                try {
                    $closure($message->body);
                    $resolver->acknowledge($message);
                    Log::debug("Success consumer {$queue}: " . $message->body);
                } catch (Throwable $e) {
                    Log::error("Error consumer {$queue}: " . $e->getMessage() . json_encode($e->getTrace()));
                }
                $resolver->stopWhenProcessed();
            }, $custom + $topic);
            sleep(1);
        } while (true);
    }

}
