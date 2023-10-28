<?php

namespace App\Http\Controllers\Api;

use App\Adapter\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use BRCas\CA\Exceptions\DomainNotFoundException;
use BRCas\CA\Exceptions\UseCaseException;
use CodePix\Bank\Application\UseCases\Transaction\DebitUseCase;
use CodePix\Bank\Application\UseCases\Transaction\FindUseCase;
use Costa\Entity\Exceptions\NotificationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * @throws NotificationException
     * @throws UseCaseException
     * @throws DomainNotFoundException
     */
    public function store(TransactionRequest $transactionRequest, DebitUseCase $useCase): JsonResponse
    {
        $data = $transactionRequest->validated();
        $response = $useCase->exec($data["account"], $data["description"], $data["value"], $data["kind"], $data["key"]);
        return ApiAdapter::json($response->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @throws DomainNotFoundException
     */
    public function show(Request $request, FindUseCase $findUseCase): JsonResponse
    {
        $response = $findUseCase->exec($request->transaction);
        return ApiAdapter::json($response->toArray(), Response::HTTP_OK);
    }
}
