<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\PixKey;

use App\Models\Transaction;

use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;

beforeEach(function(){
    $this->account = Account::factory()->create();

    $this->defaults = [
        'account' => $this->account->id,
        'value' => 50,
        'description' => 'testing',
    ];
});

describe("TransactionController Feature Test", function(){
    test("creating a multiple pix", function ($data) {
        $response = postJson(route('api.transaction.store'), $data + $this->defaults)->assertJsonStructure([
            'data' => [
                'account' => [
                    'balance',
                    'name',
                    'id',
                    'created_at',
                    'updated_at',
                ],
                'kind',
                'key',
                'id',
                'created_at',
                'updated_at',
            ],
        ]);

        expect(true)->toBeDatabaseResponse($response, Transaction::class, ['account']);
        assertEquals((string) $this->account->id, $response->json('data.account.id'));
    })->with([
        [['key' => 'test@test.com', 'kind' => 'email']],
        [['key' => '(19) 98870-9090', 'kind' => 'phone']],
        [['kind' => 'id', 'key' => (string) str()->uuid()]],
        [['key' => '84.209.990/0001-62', 'kind' => 'document']],
    ]);

    describe("validation fields", function() {
        test("validating required fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.required', ['attribute' => __($field)]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [[], ['value', 'account', 'kind', 'key']],
        ]);

        test("validating numeric fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.numeric', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['value' => 'a'], ['value']],
        ]);

        test("validating numeric with min fields", function ($data, $fields, float $min) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.min.numeric', ['attribute' => $field, 'min' => $min]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['value' => -1], ['value'], 0.01],
        ]);

        test("validating uuid fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.uuid', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'id', 'key' => 'testing'], ['key']],
        ]);

        test("validating email fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.email', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'email', 'key' => 'testing'], ['key']],
        ]);

        test("validating phone fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect('O campo key não é um celular com DDD válido.')->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'phone', 'key' => 'testing'], ['key']],
        ]);

        test("validating document fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect('O campo key não é um CPF ou CNPJ válido.')->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'document', 'key' => 'testing'], ['key']],
        ]);

        test("validating enum fields", function ($data, $fields) {
            $response = postJson(route('api.transaction.store'), $data);
            foreach ($fields as $field) {
                expect(__('validation.enum', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => '___'], ['kind']],
        ]);
    });
});
