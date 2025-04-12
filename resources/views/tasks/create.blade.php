@extends('layouts.app')

@section('content')
<h1>Create Task</h1>

<form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div>
        <label>Title:</label>
        <input type="text" name="title" value="{{ old('title') }}" required maxlength="100">
        @error('title') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Content:</label>
        <textarea name="content" required>{{ old('content') }}</textarea>
        @error('content') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Status:</label>
        <select name="status" required>
            <option value="to-do">To-do</option>
            <option value="in-progress">In-progress</option>
            <option value="done">Done</option>
        </select>
        @error('status') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>Attachment (Image only, max 4MB):</label>
        <input type="file" name="image" accept="image/*">
        @error('image') <div>{{ $message }}</div> @enderror
    </div>

    <div>
        <label>
            <input type="checkbox" name="is_draft" value="1" {{ old('is_draft') ? 'checked' : '' }}>
            Save as Draft
        </label>
    </div>

    <button type="submit">Save Task</button>
</form>
@endsection
