<?php

declare(strict_types=1);

namespace Tests\Stubs;

use CodePix\Bank\Domain\Enum\EnumPixType;
use CodePix\Bank\Integration\DTO\RegisterOutput;
use CodePix\Bank\Integration\PixKeyIntegrationInterface;

class PixKeyIntegrationStub implements PixKeyIntegrationInterface
{
    public function register(EnumPixType $kind, ?string $key): ?RegisterOutput
    {
        return new RegisterOutput($key ?: (string) str()->uuid());
    }

}
