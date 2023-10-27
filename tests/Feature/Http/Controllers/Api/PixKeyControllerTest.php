<?php

declare(strict_types=1);

use App\Models\PixKey;

use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(fn() => $this->defaults = ['bank' => (string)str()->uuid()]);

describe("PixKeyController Feature Test", function () {
    test("creating a multiple pix", function ($data) {
        $response = postJson('/api/pix', $this->defaults + $data)->assertJsonStructure([
            'data' => [
                'bank',
                'kind',
                'key',
                'id',
                'created_at',
                'updated_at',
            ],
        ]);
        expect(true)->toBeDatabaseResponse($response, PixKey::class);
    })->with([
        [['key' => 'test@test.com', 'kind' => 'email']],
        [['key' => '(19) 98870-9090', 'kind' => 'phone']],
        [['kind' => 'id']],
        [['key' => '84.209.990/0001-62', 'kind' => 'document']],
    ]);

    test("registering a pix passing the kind id with a defined value", function () {
        $data = ['kind' => 'id', 'key' => 'testing'];
        $response = postJson('/api/pix', $this->defaults + $data);
        assertNotEquals('testing', $response->json('data.key'));
        expect(true)->toBeDatabaseResponse($response, PixKey::class);
    });

    test("creating a new pix but it already exists in our database", function () {
        PixKey::factory()->create(
            $pix = [
                'kind' => 'email',
                'key' => 'test@test.com',
            ]
        );

        postJson('/api/pix', $this->defaults + $pix)->assertStatus(422)->assertJsonStructure([
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

    test("validating required fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect(__('validation.required', ['attribute' => $field]))->toBeValidateResponse($response, $field);
        }
    })->with([
        [[], ['bank', 'kind']],
        [['bank' => '3c05a2c6-5ebe-4dee-9460-3ba5cade0992'], ['kind']],
        [['bank' => '3c05a2c6-5ebe-4dee-9460-3ba5cade0992', 'kind' => 'email'], ['key']],
        [['bank' => '3c05a2c6-5ebe-4dee-9460-3ba5cade0992', 'kind' => 'phone'], ['key']],
        [['bank' => '3c05a2c6-5ebe-4dee-9460-3ba5cade0992', 'kind' => 'document'], ['key']],
    ]);

    test("validating uuid fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect(__('validation.uuid', ['attribute' => $field]))->toBeValidateResponse($response, $field);
        }
    })->with([
        [['bank' => 'testing'], ['bank']],
    ]);

    test("validating email fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect(__('validation.email', ['attribute' => $field]))->toBeValidateResponse($response, $field);
        }
    })->with([
        [['kind' => 'email', 'key' => 'testing'], ['key']],
    ]);

    test("validating phone fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect('O campo key não é um celular com DDD válido.')->toBeValidateResponse($response, $field);
        }
    })->with([
        [['kind' => 'phone', 'key' => 'testing'], ['key']],
    ]);

    test("validating document fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect('O campo key não é um CPF ou CNPJ válido.')->toBeValidateResponse($response, $field);
        }
    })->with([
        [['kind' => 'document', 'key' => 'testing'], ['key']],
    ]);

    test("validating enum fields", function ($data, $fields) {
        $response = postJson('/api/pix', $data);
        foreach ($fields as $field) {
            expect(__('validation.enum', ['attribute' => $field]))->toBeValidateResponse($response, $field);
        }
    })->with([
        [['kind' => '___'], ['kind']],
    ]);
});
