<?php

declare(strict_types=1);

namespace App\Adapter;

use App\Http\Resources\DefaultResource;
use BRCas\CA\Contracts\Items\PaginationInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiAdapter
{
    public function __construct(
        // private ?PaginationInterface $response = null
        private PaginationInterface $response
    ) {
    }

    public function toJson(): AnonymousResourceCollection
    {
        return DefaultResource::collection($this->response->items())
            ->additional([
                'meta' => [
                    'total' => $this->response->total(),
                    'current_page' => $this->response->currentPage(),
                    'last_page' => $this->response->lastPage(),
                    'first_page' => $this->response->firstPage(),
                    'per_page' => $this->response->perPage(),
                    'to' => $this->response->to(),
                    'from' => $this->response->from(),
                ],
            ]);
    }

    public static function json(
        object|array $data,
        int $statusCode = 200,
        $resource = DefaultResource::class
    ): JsonResponse
    {
        return (new $resource($data))
            ->response()
            ->setStatusCode($statusCode);
    }
}
