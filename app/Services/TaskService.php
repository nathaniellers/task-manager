<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function store(array $data)
    {
        if (isset($data['image'])) {
            $path = $data['image']->store('tasks', 'public');
            $data['image_path'] = '/storage/' . $path;
        }
    
        $data['is_draft'] = isset($data['is_draft']) ? (bool) $data['is_draft'] : false;
    
        return $this->taskRepository->create($data);
    }
    
    public function update(Task $task, array $data)
    {
        if (!empty($data['remove_image']) && $task->image_path) {
            $relativePath = str_replace('/storage/', '', $task->image_path);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
            $data['image_path'] = null;
        }

        if (isset($data['image'])) {
            $path = $data['image']->store('tasks', 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        $data['is_draft'] = isset($data['is_draft']) ? (bool) $data['is_draft'] : false;
        $task->fill($data);
        $this->taskRepository->save($task);

        return $task;
    }

    public function updateSubtasksStatus(Task $task, array $subtasks)
    {
        foreach ($subtasks as $subtaskData) {
            $subtask = $task->subtasks()->find($subtaskData['id']);
            if ($subtask) {
                $subtask->status = $subtaskData['status'];
                $subtask->save();
            }
        }
    }

    public function checkIfAllSubtasksDone(array $subtasks): bool
    {
        foreach ($subtasks as $subtaskData) {
            if ($subtaskData['status'] !== 'done') {
                return false;
            }
        }
        return true;
    }
    
    public function delete(Task $task)
    {
        if ($task->image_path && Storage::disk('public')->exists($task->image_path)) {
            Storage::disk('public')->delete($task->image_path);
        }
        return $this->taskRepository->delete($task);
    }

    public function softDelete($id): bool
    {
        $task = $this->taskRepository->findById($id);

        if (!$task) {
            return false;
        }

        $task->status = 'deleted';
        $task->deleted_at = now();
        return $this->taskRepository->save($task);
    }
}
