@extends('layouts.app')

@section('content')
<h1>Edit Task</h1>

<form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label>Title:</label>
        <input type="text" name="title" value="{{ old('title', $task->title) }}" required maxlength="100">
        @error('title') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Content:</label>
        <textarea name="content" required>{{ old('content', $task->content) }}</textarea>
        @error('content') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Status:</label>
        <select name="status" required>
            <option value="to-do" {{ $task->status === 'to-do' ? 'selected' : '' }}>To-do</option>
            <option value="in-progress" {{ $task->status === 'in-progress' ? 'selected' : '' }}>In-progress</option>
            <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
        </select>
        @error('status') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Current Image:</label>
        @if ($task->image_path)
            <img src="{{ asset('storage/' . $task->image_path) }}" width="100">
        @else
            <em>No image</em>
        @endif
    </div>

    <div>
        <label>Replace Image:</label>
        <input type="file" name="image" accept="image/*">
        @error('image') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>
            <input type="checkbox" name="is_draft" value="1" {{ $task->is_draft ? 'checked' : '' }}>
            Save as Draft
        </label>
    </div>

    <button type="submit">Update Task</button>
</form>
@endsection
