<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PixKeyRequest;
use App\Http\Resources\PixKeyResource;
use BRCas\CA\Exceptions\UseCaseException;
use CodePix\System\Application\UseCases\PixKey\CreateUseCase;
use Costa\Entity\Exceptions\EntityException;
use Costa\Entity\Exceptions\NotificationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PixKeyController extends Controller
{
    /**
     * @throws EntityException
     * @throws NotificationException
     * @throws UseCaseException
     */
    public function store(PixKeyRequest $pixKeyRequest, CreateUseCase $createUseCase): JsonResponse
    {
        $data = $pixKeyRequest->validated();
        $response = $createUseCase->exec($data["bank"], $data["kind"], $data["key"] ?? null);
        return (new PixKeyResource($response))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
