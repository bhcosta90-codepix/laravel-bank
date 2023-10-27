<?php

declare(strict_types=1);

use App\Console\Commands\Transaction\CompletedCommand;
use App\Models\PixKey;
use App\Models\Transaction;
use CodePix\System\Domain\Enum\EnumTransactionStatus;
use Tests\Stubs\RabbitMQStub;

use function Pest\Laravel\assertDatabaseHas;

describe("CompletedCommand Unit Test", function () {
    test("creating a transaction with error", function () {
        $transaction = Transaction::factory()->create(['status' => EnumTransactionStatus::CONFIRMED]);

        $command = new CompletedCommand();
        $command->handle(
            new RabbitMQStub([
                "id" => $transaction->id,
            ])
        );

        $transaction->refresh();
        assertDatabaseHas(Transaction::class, [
            'status' => 'completed'
        ]);
    });
});
