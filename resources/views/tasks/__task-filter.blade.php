<form method="GET" action="{{ route('tasks.index') }}" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="search" placeholder="Search title..." class="form-control" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="to-do" {{ request('status') == 'to-do' ? 'selected' : '' }}>To-do</option>
                <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In-progress</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="limit" class="form-control">
                <option value="10" {{ request('limit') == '10' ? 'selected' : '' }}>10 per page</option>
                <option value="20" {{ request('limit') == '20' ? 'selected' : '' }}>20 per page</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
    </div>
</form>
