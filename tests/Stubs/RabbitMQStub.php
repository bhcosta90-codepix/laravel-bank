<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Services\Contracts\AMQPInterface;
use App\Services\Contracts\RabbitMQInterface;
use Closure;
use Illuminate\Support\Facades\Log;

class RabbitMQStub implements AMQPInterface, RabbitMQInterface
{
    public function __construct(protected array|string $data = [])
    {
        if (is_array($this->data)) {
            $this->data = json_encode($this->data);
        }
    }

    public function publish($name, array $value = []): void
    {
        Log::driver('testing')->info(['action' => __FUNCTION__, 'name' => $name, 'value' => $value]);
    }

    public function consume(string $queue, array|string $topic, Closure $closure, array $custom = []): void
    {
        Log::driver('testing')->info(['action' => __FUNCTION__, 'queue' => $queue, 'topic' => $topic, 'custom' => $custom]);
        $closure($this->data);
    }
}
