<?php

declare(strict_types=1);

namespace System\Domain\Repositories;

use App\Models\Transaction;
use CodePix\System\Application\Repository\TransactionRepositoryInterface;
use CodePix\System\Domain\DomainTransaction;
use CodePix\System\Domain\Enum\EnumPixType;
use CodePix\System\Domain\Enum\EnumTransactionStatus;
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
        if ($this->model->create($entity->toArray())) {
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
                'kind' => EnumPixType::from($model->kind),
                'status' => EnumTransactionStatus::from($model->status),
            ];
            return DomainTransaction::make($data + $model->toArray());
        }

        return null;
    }

}
