@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Your Tasks</h1>
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createTaskModal">Add New Task</button>
    <button class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#trashModal">Trash</button>
    <form method="GET" action="{{ route('tasks.index') }}" class="mb-4" id="filterForm">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search tasks">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="to-do">To-do</option>
                    <option value="in-progress">In-progress</option>
                    <option value="done">Done</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="limit" class="form-select">
                    <option value="5">5 per page</option>
                    <option value="10" selected>10 per page</option>
                    <option value="20">20 per page</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
                <button type="button" class="btn btn-success" id="sortTitle">
                    Sort by Title <span id="sortArrow">↑</span>
                </button>
            </div>
        </div>
    </form>
    <div id="task-list" class="list-group"></div>
    <div id="pagination-container" class="mt-4"></div>
</div>

@include('tasks.modals.create')
@include('tasks.modals.view')
@include('tasks.modals.edit')
@include('tasks.modals.trash')

@vite('resources/js/app.js')

@endsection
