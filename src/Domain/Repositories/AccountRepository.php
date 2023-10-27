<?php

declare(strict_types=1);

namespace Bank\Domain\Repositories;

use App\Models\Account;
use CodePix\Bank\Application\Repository\AccountRepositoryInterface;
use CodePix\Bank\Domain\DomainAccount;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(protected Account $model){
        //
    }

    private array $fieldsUpdated = [
        'status',
    ];

    public function create(DomainAccount $entity): ?DomainAccount
    {
        dd('TODO: Implement create() method.');
    }

    public function save(DomainAccount $entity): ?DomainAccount
    {
        dd('TODO: Implement save() method.');
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
