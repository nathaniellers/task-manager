<div class="border rounded p-2 my-2 d-flex justify-content-between align-items-center" id="task-{{ $task->id }}">
    <div>
        <h5>{{ $task->title }}</h5>
        <p class="mb-1">Status: 
            <span class="badge {{ ucfirst($task->status) === 'To-do' ? 'bg-success' : (ucfirst($task->status) === 'In-progress' ? 'bg-warning' : 'bg-danger') }}">
                {{ ucfirst($task->status) }}
            </span>
        </p>
    </div>
    <div>
        <button class="btn btn-info btn-sm view-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal"
            data-title="{{ $task->title }}" data-status="{{ $task->status }}"
            data-content="{{ $task->content }}" data-created="{{ $task->created_at->format('M d, Y H:i') }}"
            data-image="{{ $task->image_path ? asset('storage/' . $task->image_path) : '' }}">
            View
        </button>
        <button class="btn btn-sm btn-warning edit-task-btn" data-bs-toggle="modal"
            data-bs-target="#editTaskModal" data-id="{{ $task->id }}" data-title="{{ $task->title }}"
            data-content="{{ $task->content }}" data-status="{{ $task->status }}"
            data-is_draft="{{ $task->is_draft }}" data-task='@json($task)'>
            Edit
        </button>
        <button type="button" data-id="{{ $task->id }}" class="btn btn-sm btn-danger delete-task-btn">Delete</button>
    </div>
</div>
