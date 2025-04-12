@extends('layouts.app')

@section('content')
<h1>{{ $task->title }}</h1>

<p><strong>Status:</strong> {{ $task->status }}</p>
<p><strong>Content:</strong><br>{{ $task->content }}</p>
<p><strong>Created:</strong> {{ $task->created_at->format('M d, Y H:i') }}</p>

@if ($task->image_path)
    <p><strong>Image:</strong><br>
        <img src="{{ asset('storage/' . $task->image_path) }}" width="200">
    </p>
@endif

<p><a href="{{ route('tasks.edit', $task) }}">Edit</a></p>
<form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?')">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>

<a href="{{ route('tasks.index') }}">← Back to Task List</a>
@endsection
