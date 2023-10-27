<?php

declare(strict_types=1);

use App\Models\Transaction;
use CodePix\System\Application\UseCases\Transaction\Status\ConfirmedUseCase;
use CodePix\System\Domain\DomainTransaction;
use CodePix\System\Domain\Enum\EnumTransactionStatus;
use CodePix\System\Domain\Events\EventTransactionConfirmed;
use Costa\Entity\Exceptions\EntityException;
use Illuminate\Support\Facades\Event;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

beforeEach(function () {
    $this->transaction = Transaction::factory()->create(['status' => EnumTransactionStatus::PENDING]);
    $this->useCase = app(ConfirmedUseCase::class);
});

describe("ConfirmedUseCase Feature Test", function () {
    test("saving a confirmed transaction", function () {
        Event::fake();

        $response = $this->useCase->exec($this->transaction->id);
        $this->transaction->refresh();

        assertInstanceOf(DomainTransaction::class, $response);
        assertEquals("confirmed", $response->status->value);
        assertEquals("confirmed", $this->transaction->status);

        Event::assertDispatched(EventTransactionConfirmed::class, function ($event) {
            $data = [
                'bank' => $this->transaction->bank,
                'id' => $this->transaction->reference,
            ];
            return $event->payload() == $data;
        });
    });

    test("exception when this transaction is open", function () {
        $transaction = Transaction::factory()->create();
        expect(fn() => $this->useCase->exec($transaction->id))->toThrow(
            new EntityException('Only pending transaction can be confirmed')
        );
    });
});
