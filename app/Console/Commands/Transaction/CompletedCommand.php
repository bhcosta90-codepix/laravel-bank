<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\System\Application\UseCases\Transaction\Status\CompletedUseCase;
use Illuminate\Console\Command;

class CompletedCommand extends Command
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
             * @var CompletedUseCase $useCase
             */
            $useCase = app(CompletedUseCase::class);
            $useCase->exec(
                id: $data['id'],
            );
        });
    }
}
