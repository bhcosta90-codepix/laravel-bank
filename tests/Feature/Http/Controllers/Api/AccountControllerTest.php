<?php

declare(strict_types=1);

use App\Models\Account;

use App\Models\PixKey;
use App\Models\Transaction;

use function Pest\Laravel\getJson;

describe("AccountController Feature Test", function(){
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
