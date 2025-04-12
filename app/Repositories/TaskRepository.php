<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function findById($id): ?Task
    {
        return Task::find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function save(Task $task): bool
    {
        return $task->save();
    }
}
