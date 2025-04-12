document.addEventListener('DOMContentLoaded', function () {
    let currentSort = {
        order_by: 'title',
        direction: 'asc'
    };

    function loadTasks(url = '') {
        const search = $('input[name="search"]').val();
        const status = $('select[name="status"]').val();
        const limit = $('select[name="limit"]').val();

        $.ajax({
            url: url || '/tasks',
            method: 'GET',
            data: {
                search: search,
                status: status,
                limit: limit,
                order_by: currentSort.order_by,
                direction: currentSort.direction
            },
            success: function (res) {
                let html = '';				
				
                res.tasks.forEach(task => {
					
					const safeSubtasks = $('<div>').text(JSON.stringify(task.subtasks)).html();
					
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${task.title}</strong><br>
								<div class="text-muted text-xs">Created: ${moment(task.created_at).format('MMM DD, YYYY')}</div>
                                <span class="badge bg-${task.status == 'done' ? 'success' : (task.status == 'in-progress' ? 'warning' : 'secondary')}">
                                    ${task.status.charAt(0).toUpperCase() + task.status.slice(1)}
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-info btn-sm view-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal"
                                    data-title="${task.title}" data-status="${task.status}" data-content="${task.content}"
                                    data-created="${task.created_at}" data-image="${task.image_path || ''}" data-is_draft="${task.is_draft}" data-subtasks='${safeSubtasks}'>
                                    View
                                </button>
                                <button class="btn btn-warning btn-sm edit-task-btn" data-bs-toggle="modal" data-bs-target="#editTaskModal"
                                    data-id="${task.id}" data-title="${task.title}" data-content="${task.content}" 
                                    data-status="${task.status}" data-image="${task.image_path || ''}" data-is_draft="${task.is_draft}" data-subtasks='${safeSubtasks}'>
                                    Edit
                                </button>
                                <button type="button" data-id="${task.id}" class="btn btn-danger btn-sm delete-task-btn">Delete</button>
                            </div>
                        </div>
                    `;
                });

                $('#task-list').html(html);
                $('#pagination-container').html(res.pagination);
            }
        });
    }

    // Initial load
    loadTasks();

    // Filter button
    $('#filterBtn').on('click', function () {
        loadTasks();
    });

    // Sorting toggle
    $('#sortTitle').on('click', function () {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        $('#sortArrow').text(currentSort.direction === 'asc' ? '↑' : '↓');
        loadTasks();
    });

    // Pagination AJAX (optional, if you handle pagination links dynamically)
    $(document).on('click', '#pagination-container .pagination a', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) loadTasks(url);
    });

	$('#createTaskForm').on('submit', function (e) {
        console.log($(this).serialize());
		// $(document).on('submit', '#createTaskForm', function (e) {
		e.preventDefault();
		const form = this;
		const formData = new FormData(form);
		$('#titleError').text('');
		const button = $(this).find('button[type="submit"]')
		button.prop('disabled', true);
		$('#titleError, #contentError, #statusError, #imageError').text('');

		$.ajax({
			url: '/tasks',
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (res) {
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: res.message || "Task created successfully!",
					confirmButtonText: 'OK'
				}).then(() => {
					location.reload(); // Reload after confirming
				});
			},
			complete: function () {
				button.prop('disabled', false);
			},
			error: function (err) {
				if (err.responseJSON?.errors?.title) {
					const errors = err.responseJSON?.errors || {};

					if (errors.title) {
						$('#titleError').text(errors.title[0]);
					}
					if (errors.content) {
						$('#contentError').text(errors.content[0]);
					}
					if (errors.status) {
						$('#statusError').text(errors.status[0]);
					}
					if (errors.image) {
						$('#imageError').text(errors.image[0]);
					}
				}
			}
		});
	});
	
	// VIEW MODAL POPULATE
	$('#taskModal').on('show.bs.modal', function (event) {
		const button = $(event.relatedTarget);
		const title = button.data('title');
		const status = button.data('status').charAt(0).toUpperCase() + button.data('status').slice(1);
		const content = button.data('content');
		const created = button.data('created');
		const image = button.data('image');
		const isDraft = button.data('is_draft');
	
		// Format date using Moment.js
		const formattedCreated = moment(created).format('MMM DD, YYYY');
	
		$('#taskTitle').text(title);
		$('#taskStatus').text(status);
		$('#taskContent').html(content);
		$('#taskCreated').text(formattedCreated);
	
		let subtasks = [];
		try {
			subtasks = button.data('subtasks') || [];
			if (typeof subtasks === 'string') {
				subtasks = JSON.parse(subtasks);
			}
		} catch (e) {
			console.error("Failed to parse subtasks", e);
			subtasks = [];
		}
	
		const $wrapper = $('#view-subtasks-wrapper');
		$wrapper.empty();
	
		// Populate subtasks with status
		if (subtasks.length === 0) {
			$wrapper.append(`<li class="list-group-item text-muted">No subtasks</li>`);
		} else {
			subtasks.forEach(sub => {
				const badgeClass = getStatusBadgeClass(sub.status);
				const item = `
					<li class="list-group-item d-flex justify-content-between align-items-center">
						<span>${sub.description}</span>
						<span class="badge bg-${badgeClass}">${sub.status}</span>
					</li>
				`;
				$wrapper.append(item);
			});
		}
	
		// Handle image
		if (image) {
			$('#taskImageWrapper').show();
			$('#taskImage').attr('src', image);
		} else {
			$('#taskImageWrapper').hide();
		}
	
		// Show draft indicator
		if (isDraft == 1 || isDraft === true || isDraft === '1') {
			$('#taskDraftInfo').show();
		} else {
			$('#taskDraftInfo').hide();
		}
	});
	
	// Helper function to get badge color class
	function getStatusBadgeClass(status) {
		switch (status) {
			case 'to-do': return 'secondary';
			case 'in-progress': return 'warning';
			case 'done': return 'success';
			default: return 'light';
		}
	}
	
	$(document).on('click', '.edit-task-btn', function () {
		const taskId = $(this).data('id');
		const title = $(this).data('title');
		const content = $(this).data('content');
		const status = $(this).data('status');
		const isDraft = $(this).data('is_draft');
		const imagePath = $(this).data('image');
		
		let subtasks = [];
		try {
			subtasks = $(this).data('subtasks');
		} catch (e) {
			console.error("Failed to parse subtasks", e);
		}
	
		const $wrapper = $('#edit-subtasks-wrapper');
		$wrapper.empty();
		
		// Populate subtasks with status
		if (subtasks.length === 0) {
			addSubtaskInput($wrapper);
		} else {
			subtasks.forEach(sub => {
				addSubtaskInput($wrapper, sub.description, sub.status);
			});
		}
	
		toggleRemoveButtons($wrapper);
	
		// Populate task data in the modal
		$('#editTaskId').val(taskId);
		$('#editTitle').val(title);
		$('#editContent').val(content);
		$('#editStatus').val(status);
		$('#editIsDraft').prop('checked', isDraft);
	
		// Image handling
		if (imagePath) {
			$('#currentTaskImage').attr('src', imagePath);
			$('#currentImageWrapper').show();
			$('#uploadImageWrapper').hide();
		} else {
			$('#uploadImageWrapper').show();
			$('#currentImageWrapper').hide();
		}
		
		$('#removeImageInput').val(0); // Reset on modal open
	});
	
	function addSubtaskInput(wrapper, description = '', status = 'to-do') {
		const subtaskGroup = $(`
			<div class="input-group mb-2">
				<input type="text" name="subtasks[]" class="form-control" placeholder="Subtask description" value="${description}">
				<select name="subtask_status[]" class="form-select ms-2" required>
					<option value="to-do" ${status === 'to-do' ? 'selected' : ''}>To-do</option>
					<option value="in-progress" ${status === 'in-progress' ? 'selected' : ''}>In-progress</option>
					<option value="done" ${status === 'done' ? 'selected' : ''}>Done</option>
				</select>
				<button type="button" class="btn btn-danger remove-subtask-btn">X</button>
			</div>
		`);
		wrapper.append(subtaskGroup);
	}
	
	function toggleRemoveButtons(wrapper) {
		const subtasks = wrapper.find('.input-group');
		if (subtasks.length === 1) {
			subtasks.find('.remove-subtask-btn').hide();
		} else {
			subtasks.find('.remove-subtask-btn').show();
		}
	}
	
	// Function to check if all subtasks are marked as "done"
	function checkAndUpdateMainTaskStatus() {
		const allSubtasksDone = $('#edit-subtasks-wrapper .input-group').toArray().every(subtaskGroup => {
			const subtaskStatus = $(subtaskGroup).find('select').val();
			return subtaskStatus === 'done';
		});
	
		if (allSubtasksDone) {
			$('#editStatus').val('done');  // Update main task status to "done"
		} else {
			// Keep the main task status as it is if not all subtasks are "done"
			$('#editStatus').val('to-do');  // Optionally, set default back to "to-do"
		}
	}
	
	$('#edit-subtasks-wrapper').on('change', 'select', function () {
		checkAndUpdateMainTaskStatus();  // Check and update main task status when subtask status changes
	});
	
	$('#edit-subtasks-wrapper').on('click', '.remove-subtask-btn', function () {
		$(this).closest('.input-group').remove();
		toggleRemoveButtons($('#edit-subtasks-wrapper'));
		checkAndUpdateMainTaskStatus();  // Re-check if all subtasks are "done" after removal
	});		
	
	$('#removeImageBtn').on('click', function () {
		$('#currentImageWrapper').hide();
		$('#uploadImageWrapper').show()
		$('#removeImageInput').val(1); // this will signal the server to remove it
	});	
	
	$('#editTaskForm').on('submit', function (e) {
		e.preventDefault();
	
		const form = this;
		const formData = new FormData(form);
	
		// Check if the task is marked as a draft
		if ($('#editIsDraft').is(':checked') || saveAsDraftClicked) {
			formData.set('is_draft', '1');
		} else {
			formData.set('is_draft', '0');
		}
	
		const taskId = $('#editTaskId').val(); // Get task ID from the hidden input
		$('#editTitleError').text('');
	
		// Gather subtask statuses
		// Ensure subtasks are being added as key-value pairs
		let subtasks = [];
		let subtaskStatus = [];

		$('#edit-subtasks-wrapper .input-group').each(function () {
			const subtask = $(this).find('input[name="subtasks[]"]').val();
			const status = $(this).find('select[name="subtask_status[]"]').val();
			subtasks.push(subtask);
			subtaskStatus.push(status);
		});

		formData.set('subtasks[]', subtasks);
		formData.set('subtask_status[]', subtaskStatus);

	
		Swal.fire({
			title: 'Are you sure?',
			text: "Do you want to update this task?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, update it!'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '/tasks/' + taskId,
					method: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function (res) {
						Swal.fire({
							icon: 'success',
							title: 'Updated!',
							text: res.message || "Task updated successfully!"
						}).then(() => {
							location.reload();
						});
					},
					error: function (err) {
						if (err.responseJSON?.errors?.title) {
							$('#editTitleError').text(err.responseJSON.errors.title[0]);
						}
					}
				});
			}
		});
	});	
	
	// REMOVE IMAGE in EDIT
	$('#removeImageBtn').on('click', function () {
		$('#currentImageWrapper').hide();
		$('#uploadImageWrapper').show();
		$('#removeImageInput').val(1);
	});
	
	// DELETE TASK (AJAX optional enhancement if you're not using form POST)
	$(document).on('click', '.delete-task-btn', function (e) {
		e.preventDefault();
		const taskId = $(this).data('id');
	
		Swal.fire({
			title: 'Are you sure?',
			text: "This task will be marked as deleted.",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: `/tasks/${taskId}`,
					method: 'DELETE',
					data: {
						_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
					success: function (res) {
						$(`#task-${taskId}`).remove();
						Swal.fire({
							icon: 'success',
							title: 'Deleted!',
							text: res.message || 'The task has been deleted.',
							timer: 1500,
							showConfirmButton: false,
						});
						loadTasks()
					},
					error: function (err) {
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: 'Failed to delete the task.',
						});
					}
				});
			}
		});
	});
	
	// Load Deleted Tasks when Trash Modal is opened
	$('#trashModal').on('show.bs.modal', function () {
		loadDeletedTasks();
	});
	
	function loadDeletedTasks() {
		$.ajax({
			url: '/tasks/deleted',
			method: 'GET',
			success: function (tasks) {
				let html = '';
	
				if (tasks.length === 0) {
					html = '<p class="text-gray-500">No deleted tasks.</p>';
				} else {
					html += '<ul>';
					tasks.forEach(task => {
						const deletionStatus = task.deletion_status;
						const recoveryButton = `<button class="recover-btn btn-primary text-white bg-blue-600 hover:bg-blue-700 font-semibold py-1 px-3 rounded" data-id="${task.id}">Recover</button>`;
						
						html += `<li class="text-sm text-red-600 mb-1 flex items-center justify-between">
									<div>
										${task.title} 
										(Deleted ${new Date(task.deleted_at).toLocaleDateString()})
										<span class="ml-2 text-xs text-gray-500">
											${deletionStatus}
										</span>
									</div>
									<div>
										${recoveryButton}
									</div>
								 </li>`;
					});
					html += '</ul>';
				}
	
				$('#deleted-task-list').html(html);
	
				// Attach event listener for recovery buttons
				$('.recover-btn').on('click', function () {
					const taskId = $(this).data('id');
					recoverTask(taskId);
				});
			},
			error: function (xhr, status, error) {
				console.log(xhr.responseText);
				Swal.fire({
					icon: 'error',
					title: 'Failed to load deleted tasks',
					text: 'There was an issue fetching the deleted tasks.',
				});
			}
		});
	}
	
	function recoverTask(taskId) {
		// Show the confirmation dialog
		Swal.fire({
			title: 'Are you sure?',
			text: 'You are about to recover this deleted task.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, recover it!',
			cancelButtonText: 'Cancel',
		}).then((result) => {
			if (result.isConfirmed) {
				const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
	
				$.ajax({
					url: `/tasks/recover/${taskId}`,
					method: 'PUT',
					headers: {
						'X-CSRF-TOKEN': csrfToken // Add the CSRF token to the request headers
					},
					success: function (response) {
						Swal.fire({
							icon: 'success',
							title: 'Task Recovered',
							text: `The task has been successfully recovered.`,
						});
						loadDeletedTasks();
						loadTasks()
					},
					error: function (xhr, status, error) {
						Swal.fire({
							icon: 'error',
							title: 'Recovery Failed',
							text: `There was an issue recovering the task.`,
						});
					}
				});	
			}
		});
	}
});
