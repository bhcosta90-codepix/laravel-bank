<?php

declare(strict_types=1);

use App\Models\PixKey;
use App\Models\Transaction;
use CodePix\System\Application\UseCases\Transaction\CreateUseCase;
use CodePix\System\Domain\Events\EventTransactionCreating;
use CodePix\System\Domain\Events\EventTransactionError;
use Illuminate\Support\Facades\Event;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

beforeEach(function () {
    $this->useCase = app(CreateUseCase::class);

    $this->pix = PixKey::factory()->create([
        'kind' => 'email',
        'key' => 'test@test.com',
    ]);
});

describe("CreateUseCase Feature Test", function () {
    test("registering a new transaction without pix", function () {
        Event::fake();

        $response = $this->useCase->exec(
            $bank = (string)str()->uuid(),
            $reference = (string)str()->uuid(),
            'testing',
            50,
            'email',
            'test1@test.com'
        );

        expect()->toBeDatabaseArray($response->toArray(), Transaction::class);
        assertEquals('error', $response->status->value);
        assertEquals("PIX not found", $response->cancelDescription);

        Event::assertDispatched(EventTransactionError::class, function ($event) use ($bank, $reference) {
            $verify = [
                'bank' => $bank,
                'id' => $reference,
            ];
            return $event->payload() == $verify;
        });
    });

    test("registering a new transaction with pix", function () {
        Event::fake();

        $response = $this->useCase->exec(
            (string)str()->uuid(),
            (string)str()->uuid(),
            'testing',
            50,
            'email',
            'test@test.com'
        );

        expect()->toBeDatabaseArray($response->toArray(), Transaction::class);
        assertEquals('pending', $response->status->value);
        assertNull($response->cancelDescription);

        Event::assertDispatched(EventTransactionCreating::class, function ($event) use ($response) {
            return $event->payload() == $response->toArray();
        });
    });
});
