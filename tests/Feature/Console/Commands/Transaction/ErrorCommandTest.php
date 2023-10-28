<?php

declare(strict_types=1);

use App\Console\Commands\Transaction\ErrorCommand;
use App\Models\PixKey;
use App\Models\Transaction;
use CodePix\Bank\Domain\Enum\EnumTransactionStatus;
use Tests\Stubs\RabbitMQServiceStub;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

beforeEach(function(){
    $this->transaction = Transaction::factory()->create([
        'reference' => '018b720a-d0ec-738a-bc5d-a25e8bd684bc',
        "kind" => "email",
        "key" => "test2@test.com",
        "description" => "testing",
        "value" => 50.0,
        'status' => EnumTransactionStatus::CONFIRMED,
    ]);
});

describe("ErrorCommand Feature Test", function () {
    test("creating a transaction with error", function () {
        $command = new ErrorCommand();
        $command->handle(
            new RabbitMQServiceStub([
                "id" => $this->transaction->id,
                "message" => 'error testing',
            ])
        );

        assertDatabaseHas('transactions', [
            'reference' => '018b720a-d0ec-738a-bc5d-a25e8bd684bc',
            "account_id" => $this->transaction->account->id,
            "status" => "error",
            "cancel_description" => 'error testing',
            "description" => "testing",
            "value" => 50.0,
            "kind" => "email",
            "key" => "test2@test.com",
            "type" => 1,
        ]);

        assertEquals(0, $this->transaction->account->balance);
    });
});
