<?php

declare(strict_types=1);

use App\Console\Commands\Transaction\CreatingCommand;
use App\Models\PixKey;
use Tests\Stubs\RabbitMQStub;

use function Pest\Laravel\assertDatabaseHas;

describe("CreatingCommand Unit Test", function () {
    test("creating a transaction with error", function () {
        $command = new CreatingCommand();
        $command->handle(
            new RabbitMQStub([
                "bank" => "4b964f8a-4d62-48e0-9418-aa19dc87426a",
                "status" => "pending",
                "cancel_description" => null,
                "account" => [
                    "balance" => 0.0,
                    "name" => "testing",
                    "id" => "018b720a-d0ec-738a-bc5d-a25e8adf4c05",
                    "created_at" => "2023-10-27 16:49:53",
                    "updated_at" => "2023-10-27 16:49:53",
                ],
                "reference" => null,
                "description" => "testing",
                "value" => 50.0,
                "kind" => "email",
                "key" => "test@test.com",
                "type" => 1,
                "id" => "018b720a-d0ec-738a-bc5d-a25e8bd684bc",
                "created_at" => "2023-10-27 16:49:53",
                "updated_at" => "2023-10-27 16:49:53",
            ])
        );

        assertDatabaseHas('transactions', [
            'reference' => '018b720a-d0ec-738a-bc5d-a25e8bd684bc',
            "bank" => "4b964f8a-4d62-48e0-9418-aa19dc87426a",
            "status" => "error",
            "cancel_description" => "PIX not found",
            "description" => "testing",
            "value" => 50.0,
            "kind" => "email",
            "key" => "test@test.com",
        ]);
    });

    test("creating a transaction with success", function () {
        PixKey::factory()->create([
            "kind" => "email",
            "key" => "test@test.com",
        ]);

        $command = new CreatingCommand();
        $command->handle(
            new RabbitMQStub([
                "bank" => "4b964f8a-4d62-48e0-9418-aa19dc87426a",
                "status" => "pending",
                "cancel_description" => null,
                "account" => [
                    "balance" => 0.0,
                    "name" => "testing",
                    "id" => "018b720a-d0ec-738a-bc5d-a25e8adf4c05",
                    "created_at" => "2023-10-27 16:49:53",
                    "updated_at" => "2023-10-27 16:49:53",
                ],
                "reference" => null,
                "description" => "testing",
                "value" => 50.0,
                "kind" => "email",
                "key" => "test@test.com",
                "type" => 1,
                "id" => "018b720a-d0ec-738a-bc5d-a25e8bd684bc",
                "created_at" => "2023-10-27 16:49:53",
                "updated_at" => "2023-10-27 16:49:53",
            ])
        );

        assertDatabaseHas('transactions', [
            'reference' => '018b720a-d0ec-738a-bc5d-a25e8bd684bc',
            "bank" => "4b964f8a-4d62-48e0-9418-aa19dc87426a",
            "status" => "pending",
            "cancel_description" => null,
            "description" => "testing",
            "value" => 50.0,
            "kind" => "email",
            "key" => "test@test.com",
        ]);
    });
});
