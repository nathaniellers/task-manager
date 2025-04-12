<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTaskForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" style="border-color:#6b7280; border-radius:0;" id="editContent" name="content" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="to-do">To-do</option>
                            <option value="in-progress">In-progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_draft" id="editIsDraft" value="1">
                        <label class="form-check-label" for="editIsDraft">Save as Draft</label>
                    </div>

                    <div class="mb-3" id="currentImageWrapper" style="display: none;">
                        <label>Current Image:</label><br>
                        <img id="currentTaskImage" src="" alt="Task Image" style="max-width: 200px; height: auto;" class="mb-2">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">Remove Image</button>
                        </div>
                    </div>

                    <div class="mb-3" id="uploadImageWrapper">
                        <label class="form-label" for="editImageInput">Upload New Image (optional)</label>
                        <input type="file" class="form-control" name="image" id="editImageInput" accept="image/*">
                    </div>

                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">

                    <div class="mb-3">
                        <label class="form-label">Subtasks</label>
                        <div id="edit-subtasks-wrapper"></div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="editAddSubtaskBtn">+ Add Subtask</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let saveAsDraftClicked = false;

    $('#saveDraftBtn').on('click', function () {
        saveAsDraftClicked = true;
        $('#editIsDraft').prop('checked', true);
        $('#editTaskForm').submit();
    });

    $('#publishTaskBtn').on('click', function () {
        saveAsDraftClicked = false;
        $('#editIsDraft').prop('checked', false);
    });

    function updateEditRemoveButtons() {
        const subtasks = $('#edit-subtasks-wrapper .input-group');
        if (subtasks.length === 1) {
            subtasks.find('.remove-subtask-btn').hide();
        } else {
            subtasks.find('.remove-subtask-btn').show();
        }
    }

    function addEditSubtaskField(value = '') {
        const group = $(`
            <div class="input-group mb-2">
                <input type="text" name="subtasks[]" class="form-control" placeholder="Subtask description" value="${value}">
                <button type="button" class="btn btn-danger remove-subtask-btn">X</button>
            </div>
        `);
        $('#edit-subtasks-wrapper').append(group);
        updateEditRemoveButtons();
    }

    $('#editAddSubtaskBtn').on('click', function () {
        addEditSubtaskField();
    });

    $('#edit-subtasks-wrapper').on('click', '.remove-subtask-btn', function () {
        $(this).closest('.input-group').remove();
        updateEditRemoveButtons();
    });

    function populateEditModal(task) {
        $('#editTaskId').val(task.id);
        $('#editTitle').val(task.title);
        $('#editContent').val(task.content);
        $('#editStatus').val(task.status);
        $('#editIsDraft').prop('checked', task.is_draft);

        if (task.image_url) {
            $('#currentTaskImage').attr('src', task.image_url);
            $('#currentImageWrapper').show();
        } else {
            $('#currentImageWrapper').hide();
        }

        $('#edit-subtasks-wrapper').empty();
        if (Array.isArray(task.subtasks) && task.subtasks.length > 0) {
            task.subtasks.forEach(st => addEditSubtaskField(st));
        } else {
            addEditSubtaskField();
        }
    }
</script> 