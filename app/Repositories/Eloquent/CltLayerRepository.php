<?php

namespace App\Repositories\Eloquent;

use App\Models\CltLayer;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CltLayerRepository implements CltLayerRepositoryInterface
{
    public function __construct(protected CltLayer $model)
    {
    }

    public function allForLayup(int $layupId): Collection
    {
        return $this->model
            ->where('layup_id', $layupId)
            ->orderBy('layer_order')
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
        $layer = $this->findOrFail($id);
        $layer->update($data);
        return $layer;
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function findByOrderAndLayup(int $layerOrder, int $layupId): ?Model
    {
        return $this->model
            ->where('layer_order', $layerOrder)
            ->where('layup_id', $layupId)
            ->first();
    }
}
