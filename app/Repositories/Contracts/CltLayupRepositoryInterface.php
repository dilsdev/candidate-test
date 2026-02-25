<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CltLayupRepositoryInterface
{
    public function allForSupplier(int $supplierId): Collection;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function findByNameAndSupplier(string $name, int $supplierId): ?Model;

    public function getWithLayers(int $id): Model;
}
