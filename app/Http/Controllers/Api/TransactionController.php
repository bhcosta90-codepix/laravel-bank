<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use BRCas\CA\Exceptions\DomainNotFoundException;
use BRCas\CA\Exceptions\UseCaseException;
use CodePix\Bank\Application\UseCases\Transaction\DebitUseCase;
use Costa\Entity\Exceptions\NotificationException;
use Illuminate\Http\JsonResponse;
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
        return (new TransactionResource($response))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
