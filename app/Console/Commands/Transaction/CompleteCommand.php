<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\Bank\Application\UseCases\Transaction\Status\CompleteUseCase;
use Illuminate\Console\Command;

class CompleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Confirmation of transaction';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQInterface $rabbitMQService): void
    {
        $rabbitMQService->consume("transaction_complete", "transaction.complete", function ($message) {
            $data = json_decode($message, true);
            /**
             * @var CompleteUseCase $useCase
            */
            $useCase = app(CompleteUseCase::class);
            $useCase->exec(
                id: $data['id'],
            );
        });
    }
}
