<?php

namespace App\Repositories\Eloquent;

use App\Models\CltLayup;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CltLayupRepository implements CltLayupRepositoryInterface
{
    public function __construct(protected CltLayup $model)
    {
    }

    public function allForSupplier(int $supplierId): Collection
    {
        return $this->model
            ->where('supplier_id', $supplierId)
            ->withCount('layers')
            ->orderBy('name')
            ->get();
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
        $layup = $this->findOrFail($id);
        $layup->update($data);
        return $layup;
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function findByNameAndSupplier(string $name, int $supplierId): ?Model
    {
        return $this->model
            ->where('name', $name)
            ->where('supplier_id', $supplierId)
            ->first();
    }

    public function getWithLayers(int $id): Model
    {
        return $this->model->with(['layers' => function ($query) {
            $query->orderBy('layer_order');
        }])->findOrFail($id);
    }
}
