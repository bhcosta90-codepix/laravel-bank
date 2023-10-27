<?php

declare(strict_types=1);

namespace Bank\Domain\Repositories;

use App\Models\Account;
use CodePix\Bank\Application\Repository\AccountRepositoryInterface;
use CodePix\Bank\Domain\DomainAccount;
use Illuminate\Support\Arr;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(protected Account $model){
        //
    }

    private array $fieldsUpdated = [
        'balance',
    ];

    public function create(DomainAccount $entity): ?DomainAccount
    {
        dd('TODO: Implement create() method.');
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

    protected function toEntity(?Account $model): ?DomainAccount
    {
        if ($model) {
            $data = [];
            return DomainAccount::make($data + $model->toArray());
        }

        return null;
    }
}
