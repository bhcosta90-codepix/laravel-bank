<?php

declare(strict_types=1);

namespace System\Domain\Repositories;

use App\Models\PixKey;
use CodePix\System\Application\Repository\PixKeyRepositoryInterface;
use CodePix\System\Domain\DomainPixKey;
use CodePix\System\Domain\Enum\EnumPixType;

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
            $data = [
                'kind' => EnumPixType::from($model->kind),
            ];
            return DomainPixKey::make($data + $model->toArray());
        }

        return null;
    }

    public function create(DomainPixKey $entity): ?DomainPixKey
    {
        if ($this->model->create($entity->toArray())) {
            return $entity;
        }
        return null;
    }
}
