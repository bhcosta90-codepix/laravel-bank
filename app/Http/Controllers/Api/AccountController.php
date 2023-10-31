<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Adapter\ApiAdapter;
use App\Http\Requests\AccountRequest;
use BRCas\CA\Exceptions\DomainNotFoundException;
use CodePix\Bank\Application\UseCases\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AccountController
{
    public function store(AccountRequest $accountRequest, Account\CreateUseCase $createUseCase): JsonResponse
    {
        $response = $createUseCase->exec($accountRequest->name, $accountRequest->document);
        return ApiAdapter::json($response->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @throws DomainNotFoundException
     */
    public function show(string $account, Account\FindUseCase $findUseCase): JsonResponse
    {
        $response = $findUseCase->exec($account);
        return ApiAdapter::json($response->toArray(), Response::HTTP_OK);
    }

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
    public function transactions(
        string $account,
        Account\MyTransactionUseCase $myTransactionUseCase
    ): AnonymousResourceCollection {
        $response = $myTransactionUseCase->exec(id: $account);
        return (new ApiAdapter($response))->toJson();
    }
}
