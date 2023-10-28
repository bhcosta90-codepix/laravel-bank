<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\PixKey;
use App\Models\Transaction;
use CodePix\Bank\Application\Repository\TransactionRepositoryInterface;
use CodePix\Bank\Domain\Events\EventTransactionCreating;
use CodePix\Bank\Domain\Events\EventTransactionError;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;

beforeEach(function(){
    $this->account = Account::factory()->create();

    $this->defaults = [
        'account' => $this->account->id,
        'value' => 50,
        'description' => 'testing',
    ];

    $this->endpoint = route('api.transaction.store', $this->account->id);
});

describe("TransactionController Feature Test", function(){
    describe("action store", function () {
        test("creating a multiple pix", function ($data) {
            Event::fake();

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

            expect(true)->toBeDatabaseResponse($response, Transaction::class, ['account']);
            assertEquals((string)$this->account->id, $response->json('data.account.id'));

            $id = $response->json('data.id');
            Event::assertDispatched(EventTransactionCreating::class, function ($event) use ($id) {
                return $event->payload()['id'] == app(TransactionRepositoryInterface::class)->find($id)->id();
            });
            Event::assertNotDispatched(EventTransactionError::class);
        })->with([
            [['key' => 'test@test.com', 'kind' => 'email']],
            [['key' => '(19) 98870-9090', 'kind' => 'phone']],
            [['kind' => 'id', 'key' => (string)str()->uuid()]],
            [['key' => '84.209.990/0001-62', 'kind' => 'document']],
        ]);

        test("registering a transaction for the same account", function () {
            Event::fake();

            $pix = PixKey::factory()->create(['account_id' => $this->account->id]);

            $data = [
                'kind' => $pix->kind,
                'key' => $pix->key,
            ];
            $response = postJson($this->endpoint, $data + $this->defaults);
            expect(true)->toBeDatabaseResponse($response, Transaction::class, ['account']);
            assertEquals("error", $response->json('data.status'));
            assertEquals("You cannot transfer to your own account", $response->json('data.cancel_description'));

            $id = $response->json('data.id');
            Event::assertDispatched(EventTransactionError::class, function ($event) use ($id) {
                return $event->payload()['id'] == app(TransactionRepositoryInterface::class)->find($id)->id();
            });
            Event::assertNotDispatched(EventTransactionCreating::class);
        });

        describe("validation fields", function () {
            test("validating required fields", function ($data, $fields) {
                $response = postJson($this->endpoint, $data);
                foreach ($fields as $field) {
                    expect(__('validation.required', ['attribute' => __($field)]))->toBeValidateResponse(
                        $response,
                        $field
                    );
                }
            })->with([
                [[], ['value', 'account', 'kind', 'key']],
            ]);

            test("validating numeric fields", function ($data, $fields) {
                $response = postJson($this->endpoint, $data);
                foreach ($fields as $field) {
                    expect(__('validation.numeric', ['attribute' => $field]))->toBeValidateResponse($response, $field);
                }
            })->with([
                [['value' => 'a'], ['value']],
            ]);

            test("validating numeric with min fields", function ($data, $fields, float $min) {
                $response = postJson($this->endpoint, $data);
                foreach ($fields as $field) {
                    expect(__('validation.min.numeric', ['attribute' => $field, 'min' => $min]))->toBeValidateResponse(
                        $response,
                        $field
                    );
                }
            })->with([
                [['value' => -1], ['value'], 0.01],
            ]);

            test("validating uuid fields", function ($data, $fields) {
                $response = postJson($this->endpoint, $data);
                foreach ($fields as $field) {
                    expect(__('validation.uuid', ['attribute' => $field]))->toBeValidateResponse($response, $field);
                }
            })->with([
                [['kind' => 'id', 'key' => 'testing'], ['key']],
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

    describe("action show", function () {
        test("show a transaction", function(){
            $transaction = Transaction::factory()->create();
            $endpointShow = route('api.transaction.show', [
                'account' => $this->account->id,
                'transaction' => $transaction->id,
            ]);

            $response = getJson($endpointShow);
            assertEquals($transaction->id, $response->json('data.id'));
        });

        test("exception when the transaction do not exist", function(){
            $endpointShow = route('api.transaction.show', [
                'account' => $this->account->id,
                'transaction' => "test",
            ]);

            getJson($endpointShow)->assertStatus(404);
        });
    });
});
