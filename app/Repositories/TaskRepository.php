<?php
namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(private Task $model) {}

    public function paginate(array $filters, int $perPage): mixed
    {
        return $this->model
            ->select([
                'id', 'title', 'description', 'status',
                'assigned_user_id', 'created_by',
                'due_date', 'created_at'
            ])
            ->with(['assignedUser', 'creator'])
            ->when(
                isset($filters['status']),
                fn($q) => $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['assigned_user_id']),
                fn($q) => $q->where('assigned_user_id', $filters['assigned_user_id'])
            )
            ->when(
                isset($filters['search']),
                fn($q) => $q->where('title', 'like', "%{$filters['search']}%")
            )
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): mixed
    {
        return $this->model
            ->select([
                'id', 'title', 'description', 'status',
                'assigned_user_id', 'created_by',
                'due_date', 'created_at', 'updated_at'
            ])
            ->with(['assignedUser', 'creator'])
            ->findOrFail($id);
    }

    public function create(array $data): mixed
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $task = $this->model->findOrFail($id);
        $task->update($data);
        return $task->fresh(['assignedUser', 'creator']);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }
}