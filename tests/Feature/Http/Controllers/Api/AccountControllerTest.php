<?php

declare(strict_types=1);

use App\Models\Account;

use App\Models\PixKey;
use App\Models\Transaction;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;

describe("AccountController Feature Test", function(){
    describe("action store", function(){
        beforeEach(function(){
            $this->endpoint = route('api.account.store');
        });

        test("execute", function(){
            $response = postJson($this->endpoint, ['name' => 'testing'])->assertJsonStructure([
                'data' => [
                    'balance',
                    'name',
                    'id',
                    'created_at',
                    'updated_at',
                ],
            ]);
            expect(true)->toBeDatabaseResponse($response, Account::class);
        });

        describe("validation fields", function() {
            test("validating required fields", function ($data, $fields) {
                $response = postJson(route('api.account.store'), $data);
                foreach ($fields as $field) {
                    expect(__('validation.required', ['attribute' => __("validation.attributes.".$field)]))->toBeValidateResponse($response, $field);
                }
            })->with([
                [[], ['name']],
            ]);

            test("validating min fields", function ($data, $fields) {
                $response = postJson(route('api.account.store'), $data);
                foreach ($fields as $field) {
                    expect(__('validation.min.string', ['attribute' => __("validation.attributes.".$field), 'min'=> 3]))->toBeValidateResponse($response, $field);
                }
            })->with([
                [['name' => 'a'], ['name']],
            ]);

            test("validating max fields", function ($data, $fields) {
                $response = postJson(route('api.account.store'), $data);
                foreach ($fields as $field) {
                    expect(__('validation.max.string', ['attribute' => __("validation.attributes.".$field), 'max'=> 100]))->toBeValidateResponse($response, $field);
                }
            })->with([
                [['name' => str_repeat('a', 101)], ['name']],
            ]);
        });
    });
    describe("action transaction", function(){
        beforeEach(function(){
            $this->account = Account::factory()->create();
            $this->endpoint = route('api.transaction.index', $this->account->id);
        });

        test("without data", function(){
            $response = getJson(route('api.transaction.index', $this->account->id));
            toBePagination($response);
        });

        test("with data", function(){
            $data = Transaction::factory(20)->create(['account_id' => $this->account->id]);
            $response = getJson(route('api.transaction.index', $this->account->id));
            toBePagination(
                response: $response,
                data: $data->toArray(),
                total: 20,
                lastPage: 2,
                to: 1,
                from: 15,
            );
        });
    });

    describe("action pix keys", function(){
        beforeEach(function(){
            $this->account = Account::factory()->create();
            $this->endpoint = route('api.pix.index', $this->account->id);
        });

        test("without data", function(){
            $response = getJson(route('api.pix.index', $this->account->id));
            toBePagination($response);
        });

        test("with data", function(){
            $data = PixKey::factory(20)->create(['account_id' => $this->account->id]);
            $response = getJson(route('api.pix.index', $this->account->id));
            toBePagination(
                response: $response,
                data: $data->toArray(),
                total: 20,
                lastPage: 2,
                to: 1,
                from: 15,
            );
        });
    });
});
