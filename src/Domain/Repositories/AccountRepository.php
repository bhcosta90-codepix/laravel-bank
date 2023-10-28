<?php

declare(strict_types=1);

namespace Bank\Domain\Repositories;

use App\Models\Account;
use App\Models\PixKey;
use App\Models\Transaction;
use Bank\Domain\Repositories\Presenters\PaginationPresenter;
use BRCas\CA\Contracts\Items\PaginationInterface;
use CodePix\Bank\Application\Repository\AccountRepositoryInterface;
use CodePix\Bank\Domain\DomainAccount;
use Illuminate\Support\Arr;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(protected Account $model, protected Transaction $transaction, protected PixKey $pixKey)
    {
        //
    }

    private array $fieldsUpdated = [
        'balance',
    ];

    public function create(DomainAccount $entity): ?DomainAccount
    {
        if ($this->model->create($entity->toArray())) {
            return $entity;
        }
        return null;
    }

    public function save(DomainAccount $entity): ?DomainAccount
    {
        if (($db = $this->model->find($entity->id())) && $db->update(
                Arr::only($entity->toArray(), $this->fieldsUpdated)
            )) {
            return $entity;
        }

        return null;
    }

    public function find(string $id): ?DomainAccount
    {
        return $this->toEntity($this->model->find($id));
    }

    public function myTransactions(DomainAccount $entity): PaginationInterface
    {
        $result = $this->transaction->where('account_id', $entity->id())
            ->orderBy('created_at', 'desc')
            ->paginate();

        return new PaginationPresenter($result);
    }

    public function myPixKeys(DomainAccount $entity): PaginationInterface
    {
        $result = $this->pixKey->where('account_id', $entity->id())
            ->orderBy('created_at', 'desc')
            ->paginate();

        return new PaginationPresenter($result);
    }

    protected function toEntity(?Account $model): ?DomainAccount
    {
        if ($model) {
            $data = [];
            return DomainAccount::make($data + $model->toArray());
        }

        return null;
    }
}
