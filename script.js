$(document).ready(function () {
    var calendarEl = $('#calendar')[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: false,
        weekNumbers: true,
        dayMaxEvents: true,
        events: [
            {
                title: 'Meeting',
                start: '2025-01-25',
                backgroundColor: '#3788d8'
            },
            {
                title: 'Holiday',
                start: '2025-01-26',
                end: '2025-01-28',
                backgroundColor: '#28a745'
            },
            {
                title: 'Conference',
                start: '2025-01-27',
                backgroundColor: '#dc3545'
            },
            {
                title: 'Team Meeting',
                start: '2025-01-28T10:30:00',
                end: '2025-01-28T12:30:00',
                backgroundColor: '#ffc107'
            }
        ],

        // Calendar configurations
        firstDay: 1, // Monday as first day
        businessHours: true,
        selectable: false,
        selectHelper: true,

        // Event handling
        eventDidMount: function (info) {
            $(info.el).tooltip({
                title: info.event.title,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },

        dateClick: function (info) {
            console.log('Clicked on: ' + info.dateStr);
        },

        eventClick: function (info) {
            console.log('Event: ' + info.event.title);
        }
    });

    calendar.render();

    // Function to add new event
    window.addEvent = function (eventData) {
        calendar.addEvent(eventData);
    };

    // Function to update calendar
    window.updateCalendar = function () {
        calendar.refetchEvents();
    };

    const $taskModal = $('#editTaskModal');
    const $form = $('#editTaskForm');

    // Handle save button click
    $('#saveTask').on('click', function () {
        if ($form[0].checkValidity()) {
            const task = {
                id: $('#taskId').val(),
                subject: $('#taskSubject').val(),
                description: $('#taskDescription').val(),
                due_by: $('#taskDueDate').val()
            };

            // Show loading state
            $('#saveTask').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );

            // If taskId is empty, it's a new task
            if (!task.id) {
                createTask(task);
            } else {
                updateTask(task);
            }

            // Close modal
            $taskModal.modal('hide');

            // Reset form
            $form[0].reset();
        } else {
            $form[0].reportValidity();
        }
    });

    // Reset form when modal is closed
    $taskModal.on('hidden.bs.modal', function () {
        $form[0].reset();
        $('#taskId').val('');
        $('#editTaskModalLabel').text('Create New Task');
        $('#saveTask').prop('disabled', false).html('Save Task');
    });

    loadTasks();
    function loadTasks() {
        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/Task/fetch_by_user_id.php`,
            data: {},
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    if (rd.data) {
                        rd.data.map(task => {
                            updateTable(task);
                        })
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: rd.message,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

            },
        })
    }
});

// Function to create new task
function createTask(task) {
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Task/create.php`,
        data: task,
        type: "POST",
        dataType: "text",
        success: function (rd) {
            rd = JSON.parse(rd);
            if (rd.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: rd.message,
                    timer: 1500
                })
                updateTable(rd.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: rd.message,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });

        },
    })

}

// Function to update existing task
function updateTask(task) {
    $.ajax({
        url: `controllers/Task/update.php?id=${task.id}`,
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(task),
        success: function (rd) {
            rd = JSON.parse(rd);
            updateTableRow(rd.data);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Task updated successfully!',
                timer: 1500
            })
        },
        error: function (xhr, status, error) {
            toastr.error('Error updating task: ' + error);
        }
    });
}

// Function to edit task (open modal with data)
function editTask(taskId) {
    $.ajax({
        url: `controllers/Task/info.php?id=${taskId}`,
        type: 'GET',
        success: function (rd) {
            rd = JSON.parse(rd);
            const task = rd.data;
            $('#taskId').val(task.id);
            $('#taskSubject').val(task.subject);
            $('#taskDescription').val(task.description);
            $('#taskDueDate').val(task.due_by);
            $('#editTaskModalLabel').text('Edit Task');

            $('#editTaskModal').modal('show');
        },
        error: function (xhr, status, error) {
            toastr.error('Error fetching task: ' + error);
        }
    });
}

// Function to delete task
function deleteTask(taskId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                html: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `controllers/Task/delete.php?id=${taskId}`,
                type: 'POST',
                success: function () {
                    // Remove the row with animation
                    $(`#task-row-${taskId}`).fadeOut(400, function () {
                        $(this).remove();
                        updateRowNumbers();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Task has been deleted successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
                },
                error: function (xhr, status, error) {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error deleting task: ' + error
                    });
                }
            });
        }
    });
}


// Function to update table with new data
function updateTable(task) {
    const rowCount = $('#taskTable tbody tr').length + 1;
    const newRow = `
        <tr id="task-row-${task.id}">
            <td class="ps-4">
                <p class="text-xs font-weight-bold mb-0">${rowCount}</p>
            </td>
            <td>
                <p class="text-xs font-weight-bold mb-0">${task.subject}</p>
            </td>
            <td>
                <p class="text-xs font-weight-bold mb-0">${task.due_by}</p>
            </td>
            <td class="text-end pe-4">
                <button class="btn btn-link text-secondary mb-0" onclick="editTask(${task.id})">
                    <i class="bi bi-pencil text-xs"></i>
                </button>
                <button class="btn btn-link text-secondary mb-0" onclick="deleteTask(${task.id})">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </td>
        </tr>
    `;

    $('#taskTable tbody').append(newRow);

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
}

// Function to update existing table row
function updateTableRow(task) {
    $(`#task-row-${task.id}`).html(`
        <td class="ps-4">
            <p class="text-xs font-weight-bold mb-0">${$(`#task-row-${task.id}`).find('td:first p').text()}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">${task.subject}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">${task.due_by}</p>
        </td>
        <td class="text-end pe-4">
            <button class="btn btn-link text-secondary mb-0" onclick="editTask(${task.id})">
                <i class="bi bi-pencil text-xs"></i>
            </button>
            <button class="btn btn-link text-secondary mb-0" onclick="deleteTask(${task.id})">
                <i class="bi bi-trash text-xs"></i>
            </button>
        </td>
    `);
}

// Function to update row numbers after deletion
function updateRowNumbers() {
    $('#taskTable tbody tr').each(function (index) {
        $(this).find('td:first p').text(index + 1);
    });
}
