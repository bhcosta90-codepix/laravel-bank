<?php

namespace App\Console\Commands\Transaction;

use App\Services\Contracts\RabbitMQInterface;
use CodePix\System\Application\UseCases\Transaction\CreateUseCase;
use CodePix\System\Application\UseCases\Transaction\Status\ConfirmedUseCase;
use Illuminate\Console\Command;

class ConfirmationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:confirmation';

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
        $rabbitMQService->consume("transaction_confirmation", "transaction.confirmation", function ($message) {
            $data = json_decode($message, true);

            /**
             * @var ConfirmedUseCase $useCase
             */
            $useCase = app(ConfirmedUseCase::class);
            $useCase->exec(
                id: $data['id'],
            );
        });
    }
}
