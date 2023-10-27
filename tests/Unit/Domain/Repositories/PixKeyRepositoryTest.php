<?php

declare(strict_types=1);

use App\Models\PixKey;
use CodePix\System\Domain\DomainPixKey;
use CodePix\System\Domain\Enum\EnumPixType;
use Costa\Entity\ValueObject\Uuid;
use System\Domain\Repositories\PixKeyRepository;

use function PHPUnit\Framework\assertNull;
use function Tests\arrayDomainPixKey;

describe("PixKeyRepository Unit Test", function () {
    test("Testing a function create", function () {
        $mock = mock(PixKey::class);
        $mock->shouldReceive('create')->andReturn(null);

        $repository = new PixKeyRepository($mock);
        $response = $repository->create(
            new DomainPixKey(...[
                "bank" => Uuid::make(),
                "kind" => EnumPixType::EMAIL,
                "key" => 'test@test.com',
            ])
        );

        assertNull($response);
    });
});
