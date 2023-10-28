<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Services\RabbitMQService;
use Bank\Domain\Integration\PixKeyIntegration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Tests\Stubs\PixKeyIntegrationStub;
use Tests\Stubs\RabbitMQServiceStub;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->beforeEach(function () {
    $this->app->singleton(RabbitMQService::class, RabbitMQServiceStub::class);
    $this->app->singleton(PixKeyIntegration::class, PixKeyIntegrationStub::class);
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeValidateResponse', function (TestResponse $response, string $field) {
    return $this->toBe($response->json('errors')[$field][0]) && $response->assertStatus(422);
});

expect()->extend('toBeDatabaseResponse', function (TestResponse $response, string|Model $table, array $except = []) {
    expect(true)->toBeDatabaseArray($response->json('data') ?: $response->json(), $table, $except);
});

expect()->extend('toBeDatabaseArray', function (array $data, string|Model $table, array $except = []) {
    assertDatabaseCount($table, 1);
    assertDatabaseHas(
        $table,
        Arr::except($data, $except + ['created_at', 'updated_at'])
    );
});

expect()->extend('toBeRemoveDateTime', function (array $data, array $verify) {
    $remove = ['updated_at', 'created_at'];
    return Arr::except($data, $remove) == Arr::except($verify, $remove);
});

function toBePagination(
    TestResponse $response,
    array $data = [],
    int $total = 0,
    int $currentPage = 1,
    int $lastPage = 1,
    int $firstPage = 0,
    int $perPage = 15,
    int $to = 0,
    int $from = 0
) {
    $response->assertJsonStructure([
        'data',
        'meta' => [
            'total',
            'current_page',
            'last_page',
            'first_page',
            'per_page',
            'to',
            'from',
        ],
    ]);

    assertEquals($total, $response->json('meta.total'));
    assertEquals($currentPage, $response->json('meta.current_page'));
    assertEquals($lastPage, $response->json('meta.last_page'));
    assertEquals($perPage, $response->json('meta.per_page'));
    assertEquals($firstPage == 0 && $total == 0 ? $firstPage : 1, $response->json('meta.first_page'));
    assertEquals($to == 0 && $total == 0 ? $to : 1, $response->json('meta.to'));
    assertEquals($from, $response->json('meta.from'));
}

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
