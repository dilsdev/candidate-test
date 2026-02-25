<?php

namespace App\Services;

use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SupplierService
{
    public function __construct(protected SupplierRepositoryInterface $repository)
    {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
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

    public function getWithLayupsAndLayers(int $id): Model
    {
        return $this->repository->getWithLayupsAndLayers($id);
    }
}
