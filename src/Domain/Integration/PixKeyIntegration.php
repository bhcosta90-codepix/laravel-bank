<?php

declare(strict_types=1);

namespace Bank\Domain\Integration;

use CodePix\Bank\Domain\Enum\EnumPixType;
use CodePix\Bank\Integration\DTO\RegisterOutput;
use CodePix\Bank\Integration\PixKeyIntegrationInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PixKeyIntegration implements PixKeyIntegrationInterface
{
    /**
     * @throws ValidationException
     */
    public function register(EnumPixType $kind, ?string $key): ?RegisterOutput
    {
        $request = Http::withHeader('Accept', 'application/json')
            ->post(config('system.endpoint.central') . '/api/pix', [
                'bank' => config('system.bank'),
                'kind' => $kind->value,
                'key' => $key
            ]);

        $status = $request->status();
        $response = $request->json();

        if ($status !== Response::HTTP_CREATED && $status == Response::HTTP_UNPROCESSABLE_ENTITY) {
            throw ValidationException::withMessages($response);
        }

        return ($key = $response['data']['key'])
            ? new RegisterOutput($key)
            : null;
    }

}
