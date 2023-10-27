<?php

declare(strict_types=1);

use App\Models\Transaction;
use BRCas\CA\Exceptions\DomainNotFoundException;
use CodePix\System\Application\UseCases\Transaction\FindUseCase;
use CodePix\System\Domain\DomainTransaction;

use function PHPUnit\Framework\assertInstanceOf;

beforeEach(function () {
    $this->transaction = Transaction::factory()->create();
    $this->useCase = app(FindUseCase::class);
});

describe("FindUseCase Feature Test", function () {
    test("searching a transaction", function () {
        $response = $this->useCase->exec($this->transaction->id);
        assertInstanceOf(DomainTransaction::class, $response);
        expect()->toBeRemoveDateTime($this->transaction->toArray(), $response->toArray());
    });

    test("exception -> searching a transaction", function () {
        expect(fn() => $this->useCase->exec('123456'))->toThrow(DomainNotFoundException::class);
    });
});
