<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Closure;

interface RabbitMQInterface
{
    public function publish($name, array $value = []): void;
    public function consume(string $queue, string|array $topic, Closure $closure, array $custom = []): void;
}
