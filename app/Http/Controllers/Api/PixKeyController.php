<?php

namespace App\Http\Controllers\Api;

use App\Adapter\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PixKeyRequest;
use BRCas\CA\Exceptions\DomainNotFoundException;
use BRCas\CA\Exceptions\UseCaseException;
use CodePix\Bank\Application\UseCases\PixKey\CreateUseCase;
use Costa\Entity\Exceptions\EntityException;
use Costa\Entity\Exceptions\NotificationException;
use Illuminate\Http\JsonResponse;

class PixKeyController extends Controller
{
    /**
     * @throws EntityException
     * @throws NotificationException
     * @throws UseCaseException
     * @throws DomainNotFoundException
     */
    public function store(PixKeyRequest $pixKeyRequest, CreateUseCase $createUseCase): JsonResponse
    {
        $data = $pixKeyRequest->validated();
        $response = $createUseCase->exec($data["account"], $data["kind"], $data["key"] ?? null);
        return ApiAdapter::json($response->toArray());
    }
}
