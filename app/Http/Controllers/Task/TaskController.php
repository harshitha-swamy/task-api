<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'assigned_user_id', 'due_date', 'per_page']);
        $tasks = $this->taskService->list($request->user(), $filters);

        return response()->json([
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page'    => $tasks->lastPage(),
                'per_page'     => $tasks->perPage(),
                'total'        => $tasks->total(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => new TaskResource($task),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $task = $this->taskService->findById($request->user(), $id);

        return response()->json(['data' => new TaskResource($task)]);
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->update($request->user(), $id, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully.',
            'data'    => new TaskResource($task),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->taskService->delete($request->user(), $id);

        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $stats = $this->taskService->getDashboardStats($request->user());

        return response()->json(['data' => $stats]);
    }
}