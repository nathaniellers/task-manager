<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="taskTitle"></h4>
                <p><strong>Status:</strong> <span id="taskStatus"></span></p>
                <p><strong>Content:</strong><br><span id="taskContent"></span></p>
                <p><strong>Created:</strong> <span id="taskCreated"></span></p>
                
                <p id="taskDraftInfo" style="display: none;">
                    <strong>Note:</strong> <span class="badge bg-secondary">This task is saved as a draft.</span>
                </p>

                <div id="taskImageWrapper" style="display: none;">
                    <p><strong>Image:</strong><br>
                        <img id="taskImage" src="" alt="Task Image" width="200">
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Subtasks</strong></label>
                    <ul class="list-group" id="view-subtasks-wrapper">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
