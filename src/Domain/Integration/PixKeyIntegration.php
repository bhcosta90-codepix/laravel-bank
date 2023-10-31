<?php

declare(strict_types=1);

namespace Bank\Domain\Integration;

use CodePix\Bank\Domain\Enum\EnumPixType;
use CodePix\Bank\Integration\DTO\RegisterOutput;
use CodePix\Bank\Integration\PixKeyIntegrationInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PixKeyIntegration implements PixKeyIntegrationInterface
{
    /**
     * @throws ValidationException
     */
    public function register(EnumPixType $kind, ?string $key): ?RegisterOutput
    {
        $request = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer " . $this->getToken(),
        ])
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

    private function getToken(): string
    {
        $token = 'token_' . config('system.bank');

        if ($response = Cache::get($token)) {
            return (string) $response;
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post(config('system.endpoint.central') . '/oauth/token', [
            "grant_type" => "client_credentials",
            "client_id" => config('system.client_id'),
            "client_secret" => config('system.client_secret'),
            "scope" => "register-pix-keys",
        ]);

        return (string) Cache::remember($token, $response->json('expires_in') - 1, fn() => $response->json('access_token'));
    }

}
