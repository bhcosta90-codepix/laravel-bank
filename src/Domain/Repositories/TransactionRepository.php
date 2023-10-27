<?php

declare(strict_types=1);

namespace Bank\Domain\Repositories;

use App\Models\Transaction;
use CodePix\Bank\Application\Repository\TransactionRepositoryInterface;
use CodePix\Bank\Domain\DomainAccount;
use CodePix\Bank\Domain\DomainTransaction;
use CodePix\Bank\Domain\Enum\EnumPixType;
use CodePix\Bank\Domain\Enum\EnumTransactionStatus;
use CodePix\Bank\Domain\Enum\EnumTransactionType;
use Illuminate\Support\Arr;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected Transaction $model){
        //
    }

    private array $fieldsUpdated = [
        'status',
    ];

    public function create(DomainTransaction $entity): ?DomainTransaction
    {
        $data = [
            'account_id' => $entity->account->id(),
        ];
        if ($this->model->create($data + $entity->toArray())) {
            return $entity;
        }

        return null;
    }

    public function save(DomainTransaction $entity): ?DomainTransaction
    {
        if (($db = $this->model->find($entity->id())) && $db->update(
                Arr::only($entity->toArray(), $this->fieldsUpdated)
            )) {
            return $entity;
        }

        return null;
    }

    public function find(string $id): ?DomainTransaction
    {
        return $this->toEntity($this->model->find($id));
    }

    protected function toEntity(?Transaction $model): ?DomainTransaction
    {
        if ($model) {
            $data = [
                'account' => DomainAccount::make($model->account->toArray()),
                'kind' => EnumPixType::from($model->kind),
                'status' => EnumTransactionStatus::from($model->status),
                'type' => EnumTransactionType::from($model->type),
            ];

            $dataTransaction = Arr::except($model->toArray(), ['account_id']);

            return DomainTransaction::make($data + $dataTransaction);
        }

        return null;
    }

}
