<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function __construct(protected Supplier $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->withCount('layups')->orderBy('name')->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $supplier = $this->findOrFail($id);
        $supplier->update($data);
        return $supplier;
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function getWithLayupsAndLayers(int $id): Model
    {
        return $this->model->with(['layups.layers' => function ($query) {
            $query->orderBy('layer_order');
        }])->findOrFail($id);
    }
}
