<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\System\Application\UseCases\Transaction\CreateUseCase;
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
             * @var CreateUseCase $useCase
             */
            $useCase = app(CreateUseCase::class);
            $useCase->exec(
                bank: '4b964f8a-4d62-48e0-9418-aa19dc87426a',
                id: $data['id'],
                description: $data['description'],
                value: $data['value'],
                kind: $data['kind'],
                key: $data['key']
            );
        });
    }
}
