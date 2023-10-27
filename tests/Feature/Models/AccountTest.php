<?php

declare(strict_types=1);

use App\Models\Account;

use function PHPUnit\Framework\assertEquals;

describe("AccountTest", function(){
    test("registering pix in the account", function(){
        $account = Account::factory()->create();
        $account->pix()->create([
            'kind' => 'email',
            'key' => 'test@test.com'
        ]);

        assertEquals([[
            'kind' => 'email',
            'key' => 'test@test.com'
        ]], $account->pix()->get(['kind', 'key'])->toArray());
    });
});
