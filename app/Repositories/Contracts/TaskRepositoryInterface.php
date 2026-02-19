<?php
namespace App\Repositories\Contracts;

interface TaskRepositoryInterface
{
    public function paginate(array $filters, int $perPage): mixed;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}