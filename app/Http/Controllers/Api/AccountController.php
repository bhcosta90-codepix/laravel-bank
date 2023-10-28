<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Adapter\ApiAdapter;
use BRCas\CA\Exceptions\DomainNotFoundException;
use CodePix\Bank\Application\UseCases\Account;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController
{
    /**
     * @throws DomainNotFoundException
     */
    public function pixKeys(
        string $account,
        Account\MyPixKeyUseCase $myPixKeyUseCase
    ): AnonymousResourceCollection {
        $response = $myPixKeyUseCase->exec(id: $account);
        return (new ApiAdapter($response))->toJson();
    }

    /**
     * @throws DomainNotFoundException
     */
    public function transaction(
        string $account,
        Account\MyTransactionUseCase $myTransactionUseCase
    ): AnonymousResourceCollection {
        $response = $myTransactionUseCase->exec(id: $account);
        return (new ApiAdapter($response))->toJson();
    }
}
