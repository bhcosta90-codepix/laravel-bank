<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\Bank\Application\UseCases\Transaction\Status\ErrorUseCase;
use Illuminate\Console\Command;

class ErrorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:error';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Error of transaction';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQInterface $rabbitMQService): void
    {
        $rabbitMQService->consume("transaction_error", "transaction.error", function ($message) {
            $data = json_decode($message, true);
            /**
             * @var ErrorUseCase $useCase
             */
            $useCase = app(ErrorUseCase::class);
            $useCase->exec(
                id: $data['id'],
                message: $data['message'],
            );
        });
    }
}
