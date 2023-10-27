<?php

declare(strict_types=1);

use App\Models\Transaction;
use CodePix\System\Domain\DomainTransaction;
use CodePix\System\Domain\Enum\EnumPixType;
use Costa\Entity\ValueObject\Uuid;
use System\Domain\Repositories\TransactionRepository;

use function PHPUnit\Framework\assertNull;

describe("TransactionRepository Unit Test", function () {
    test("Testing a function create", function () {
        $mock = mock(Transaction::class);
        $mock->shouldReceive('create')->andReturn(null);

        $repository = new TransactionRepository($mock);

        $response = $repository->create(
            new DomainTransaction(...[
                "bank" => Uuid::make(),
                "reference" => Uuid::make(),
                "description" => 'testing',
                "value" => 50,
                "kind" => EnumPixType::EMAIL,
                "key" => "test@test.com",
            ])
        );

        assertNull($response);
    });
});
