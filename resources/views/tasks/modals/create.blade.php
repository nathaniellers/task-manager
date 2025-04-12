<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
				<form id="createTaskForm" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="mb-3">
						<label for="title" class="form-label">Title</label>
						<input type="text" class="form-control" name="title" id="title" required maxlength="100">
						<div id="titleError" class="text-danger"></div>
					</div>

					<div class="mb-3">
						<label for="content" class="form-label">Content</label>
						<textarea style="border-color:#6b7280; border-radius:0;" class="form-control" name="content" required>{{ old('content') }}</textarea>
						@error('content') <div class="text-danger">{{ $message }}</div> @enderror
					</div>

					<div class="mb-3">
						<label for="status" class="form-label">Status</label>
						<select name="status" class="form-select" required>
							<option value="to-do">To-do</option>
							<option value="in-progress">In-progress</option>
							<option value="done">Done</option>
						</select>
						@error('status') <div class="text-danger">{{ $message }}</div> @enderror
					</div>

					<div class="mb-3">
						<label for="image" class="form-label">Attachment (Image only, max 4MB)</label>
						<input type="file" class="form-control" name="image" accept="image/*">
						@error('image') <div class="text-danger">{{ $message }}</div> @enderror
					</div>

					<div class="mb-3 form-check">
						<input type="checkbox" class="form-check-input" name="is_draft" value="1"
							{{ old('is_draft') ? 'checked' : '' }}>
						<label class="form-check-label" for="is_draft">Save as Draft</label>
					</div>

					<div class="mb-3">
						<label class="form-label">Subtasks</label>
						<div id="subtasks-wrapper">
							<div class="input-group mb-2">
								<input type="text" name="subtasks[0][description]" class="form-control" placeholder="Subtask description">
								<select name="subtasks[0][status]" class="form-select ms-2" style="max-width: 150px;">
									<option value="to-do">To-do</option>
									<option value="in-progress">In-progress</option>
									<option value="done">Done</option>
								</select>
								<button type="button" class="btn btn-danger ms-2 remove-subtask-btn">X</button>
							</div>
						</div>
						<button type="button" class="btn btn-outline-secondary btn-sm" id="addSubtaskBtn">+ Add Subtask</button>
					</div>

					<button type="submit" class="btn btn-primary">Save Task</button>
				</form>
            </div>
        </div>
    </div>
</div>

<script>
	let subtaskIndex = 1;

	function updateRemoveButtons() {	
		const total = $('#subtasks-wrapper .input-group').length;
		$('#subtasks-wrapper .remove-subtask-btn').toggle(total > 1);
	}

	function getSubtaskGroup(index) {
		return `
			<div class="input-group mb-2">
				<input type="text" name="subtasks[${index}][description]" class="form-control" placeholder="Subtask description">
				<select name="subtasks[${index}][status]" class="form-select ms-2" style="max-width: 150px;">
					<option value="to-do">To-do</option>
					<option value="in-progress">In-progress</option>
					<option value="done">Done</option>
				</select>
				<button type="button" class="btn btn-danger ms-2 remove-subtask-btn">X</button>
			</div>
		`;
	}

	$('#addSubtaskBtn').on('click', function () {
		$('#subtasks-wrapper').append(getSubtaskGroup(subtaskIndex));
		subtaskIndex++;
		updateRemoveButtons();
	});

	$('#subtasks-wrapper').on('click', '.remove-subtask-btn', function () {
		$(this).closest('.input-group').remove();
		updateRemoveButtons();
	});

	updateRemoveButtons();
</script>
