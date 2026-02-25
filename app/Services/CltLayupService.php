<?php

namespace App\Services;

use App\Repositories\Contracts\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CltLayupService
{
    public function __construct(protected CltLayupRepositoryInterface $repository)
    {
    }

    public function getAllForSupplier(int $supplierId): Collection
    {
        return $this->repository->allForSupplier($supplierId);
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

    public function getWithLayers(int $id): Model
    {
        return $this->repository->getWithLayers($id);
    }
}
