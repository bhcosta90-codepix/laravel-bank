<?php

declare(strict_types=1);

use App\Models\Transaction;
use CodePix\System\Application\UseCases\Transaction\Status\CompletedUseCase;
use CodePix\System\Domain\DomainTransaction;
use CodePix\System\Domain\Enum\EnumTransactionStatus;
use Costa\Entity\Exceptions\EntityException;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

beforeEach(function () {
    $this->transaction = Transaction::factory()->create(['status' => EnumTransactionStatus::CONFIRMED]);
    $this->useCase = app(CompletedUseCase::class);
});

describe("CompletedUseCase Feature Test", function () {
    test("saving a completed transaction", function () {
        $response = $this->useCase->exec($this->transaction->id);
        $this->transaction->refresh();

        assertInstanceOf(DomainTransaction::class, $response);
        assertEquals("completed", $response->status->value);
        assertEquals("completed", $this->transaction->status);
    });

    test("exception when this transaction is open", function () {
        $transaction = Transaction::factory()->create();
        expect(fn() => $this->useCase->exec($transaction->id))->toThrow(
            new EntityException('Only confirmed transactions can be completed')
        );
    });

    test("exception when this transaction is pending", function () {
        $transaction = Transaction::factory()->create(['status' => EnumTransactionStatus::PENDING]);
        expect(fn() => $this->useCase->exec($transaction->id))->toThrow(
            new EntityException('Only confirmed transactions can be completed')
        );
    });
});
