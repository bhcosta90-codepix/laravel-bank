<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\Bank\Application\UseCases\Transaction\CreditUseCase;
use Illuminate\Console\Command;

class CreatingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:creating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating the transaction';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQInterface $rabbitMQService): void
    {
        $rabbitMQService->consume("transaction_creating", "transaction.creating", function ($message) {
            $data = json_decode($message, true);

            /**
             * @var CreditUseCase $useCase
             */
            $useCase = app(CreditUseCase::class);
            $useCase->exec(
                id: $data['id'],
                description: $data['description'],
                value: $data['value'],
                kind: $data['kind'],
                key: $data['key']
            );
        });
    }
}
