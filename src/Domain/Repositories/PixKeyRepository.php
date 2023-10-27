<?php

declare(strict_types=1);

namespace Bank\Domain\Repositories;

use App\Models\PixKey;
use CodePix\Bank\Application\Repository\PixKeyRepositoryInterface;
use CodePix\Bank\Domain\DomainAccount;
use CodePix\Bank\Domain\DomainPixKey;
use CodePix\Bank\Domain\Enum\EnumPixType;
use Illuminate\Support\Arr;

class PixKeyRepository implements PixKeyRepositoryInterface
{
    public function __construct(protected PixKey $model){
        //
    }
    public function find(EnumPixType $kind, string $key): ?DomainPixKey
    {
        return $this->toEntity($this->model->where('kind', $kind)->where('key', $key)->first());
    }

    protected function toEntity(?PixKey $model): ?DomainPixKey
    {
        if ($model) {
            $dataDomainPix = Arr::except($model->toArray(), [
                'account_id',
            ]);

            $data = [
                'kind' => EnumPixType::from($model->kind),
                'account' => DomainAccount::make($model->account->toArray()),
            ];
            return DomainPixKey::make($data + $dataDomainPix);
        }

        return null;
    }

    public function create(DomainPixKey $entity): ?DomainPixKey
    {
        $data = [
            'account_id' => $entity->account->id(),
        ];

        if ($this->model->create($data + $entity->toArray())) {
            return $entity;
        }
        return null;
    }
}
