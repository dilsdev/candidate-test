<?php

namespace App\Services;

use App\Repositories\Contracts\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CltLayerService
{
    public function __construct(protected CltLayerRepositoryInterface $repository)
    {
    }

    public function getAllForLayup(int $layupId): Collection
    {
        return $this->repository->allForLayup($layupId);
    }

    public function find(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
