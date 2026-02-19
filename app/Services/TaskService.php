<?php

namespace App\Services;

use App\Jobs\SendTaskAssignedNotification;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private PermissionService $permissionService
    ) {}

    public function list(User $user, array $filters): mixed
    {
        $perPage = min($filters['per_page'] ?? 25, 100);

        if (!$this->permissionService->hasPermission($user, 'tasks.view_all')) {
            $filters['assigned_user_id'] = $user->id;
        }

        return $this->taskRepository->paginate($filters, $perPage);
    }

    public function findById(User $user, int $id): mixed
    {
        $task = $this->taskRepository->findById($id);
        $this->authorizeTaskAction($user, $task);
        return $task;
    }

    public function create(User $user, array $data): mixed
    {
        $data['created_by'] = $user->id;
        $task = $this->taskRepository->create($data);

        if (!empty($data['assigned_user_id'])) {
            SendTaskAssignedNotification::dispatch($task)
                ->delay(now()->addSeconds(5));
        }

        $this->clearDashboardCache();

        return $task;
    }

    public function update(User $user, int $id, array $data): mixed
    {
        $task = $this->taskRepository->findById($id);
        $this->authorizeTaskAction($user, $task);

        $previousAssignee = $task->assigned_user_id;
        $updatedTask = $this->taskRepository->update($id, $data);

        if (
            isset($data['assigned_user_id']) &&
            $data['assigned_user_id'] !== $previousAssignee
        ) {
            SendTaskAssignedNotification::dispatch($updatedTask)
                ->delay(now()->addSeconds(5));
        }

        $this->clearDashboardCache();

        return $updatedTask;
    }

    public function delete(User $user, int $id): bool
    {
        $task = $this->taskRepository->findById($id);
        $this->authorizeTaskAction($user, $task);
        $this->clearDashboardCache();
        return $this->taskRepository->delete($id);
    }

    public function getDashboardStats(User $user): array
    {
        $cacheKey = 'dashboard_stats_' . $user->id;

        return Cache::remember($cacheKey, 300, function () use ($user) {
            $query = Task::selectRaw('status, COUNT(*) as count')->groupBy('status');

            if (!$this->permissionService->hasPermission($user, 'tasks.view_all')) {
                $query->where('assigned_user_id', $user->id);
            }

            return $query->pluck('count', 'status')->toArray();
        });
    }

    private function authorizeTaskAction(User $user, Task $task): void
    {
        $canManageAll = $this->permissionService->hasPermission($user, 'tasks.manage_all');

        if (!$canManageAll && $task->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function clearDashboardCache(): void
    {
        Cache::forget('dashboard_stats');
    }
}