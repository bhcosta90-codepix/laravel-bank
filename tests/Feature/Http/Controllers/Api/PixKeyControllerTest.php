<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\PixKey;

use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(function(){
    $this->account = Account::factory()->create();

    $this->defaults = [
        'account' => $this->account->id,
    ];

    $this->endpoint = route('api.pix.store', $this->account->id);
});

describe("PixKeyController Feature Test", function () {
    test("creating a multiple pix", function ($data) {
        $response = postJson($this->endpoint, $data + $this->defaults)->assertJsonStructure([
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
        expect(true)->toBeDatabaseResponse($response, PixKey::class, ['account']);
        assertEquals((string) $this->account->id, $response->json('data.account.id'));
    })->with([
        [['key' => 'test@test.com', 'kind' => 'email']],
        [['key' => '(19) 98870-9090', 'kind' => 'phone']],
        [['kind' => 'id']],
        [['key' => '84.209.990/0001-62', 'kind' => 'document']],
    ]);

    test("registering a pix passing the kind id with a defined value", function () {
        $data = ['kind' => 'id', 'key' => 'testing'];
        $response = postJson($this->endpoint, $data + $this->defaults);
        assertNotEquals('testing', $response->json('data.key'));
        expect(true)->toBeDatabaseResponse($response, PixKey::class, ['account']);
    });

    test("creating a new pix but it already exists in our database", function () {
        PixKey::factory()->create(
            $pix = [
                'kind' => 'email',
                'key' => 'test@test.com',
            ]
        );

        postJson($this->endpoint, $this->defaults + $pix)->assertStatus(422)->assertJsonStructure([
            'message',
            'errors',
        ])->assertJson([
            'message' => __('This pix is already registered in our database'),
            'errors' => [
                [
                    __('This pix is already registered in our database'),
                ],
            ],
        ]);
    });

    describe("validation fields", function() {
        test("validating required fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect(__('validation.required', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [[], ['account', 'kind']],
        ]);

        test("validating uuid fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect(__('validation.uuid', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['account' => 'testing'], ['account']],
        ]);

        test("validating exists fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect(__('validation.exists', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['account' => (string) str()->uuid()], ['account']],
        ]);

        test("validating email fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect(__('validation.email', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'email', 'key' => 'testing'], ['key']],
        ]);

        test("validating phone fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect('O campo key não é um celular com DDD válido.')->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'phone', 'key' => 'testing'], ['key']],
        ]);

        test("validating document fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect('O campo key não é um CPF ou CNPJ válido.')->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => 'document', 'key' => 'testing'], ['key']],
        ]);

        test("validating enum fields", function ($data, $fields) {
            $response = postJson($this->endpoint, $data);
            foreach ($fields as $field) {
                expect(__('validation.enum', ['attribute' => $field]))->toBeValidateResponse($response, $field);
            }
        })->with([
            [['kind' => '___'], ['kind']],
        ]);
    });
});
