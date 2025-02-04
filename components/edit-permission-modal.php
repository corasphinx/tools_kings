<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <input type="hidden" id="permissionId">
                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="permissionDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePermission">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#savePermission').click(function() {
            const id = $('#permissionId').val();
            const name = $('#permissionName').val();
            const description = $('#permissionDescription').val();

            const _thisButton = $(this);
            const _thisButtonHTML = _thisButton.text();
            _thisButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Permission/save.php`,
                type: 'POST',
                data: {
                    id,
                    name,
                    description
                },
                success: function(response) {
                    const rd = JSON.parse(response);
                    _thisButton.prop("disabled", false);
                    _thisButton.html(_thisButtonHTML);

                    if (rd.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: rd.message,
                            timer: 1500
                        }).then(() => {
                            $('#permissionModal').modal('hide');

                            if (id) {
                                // Update existing row
                                const updatedRow = `
                                    <tr>
                                        <td>${$(`#permissionsTable tbody tr button[data-id="${id}"]`).closest('tr').find('td:first').text()}</td>
                                        <td>${name}</td>
                                        <td>${description}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-permission" data-id="${id}">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-permission" data-id="${id}">Delete</button>
                                        </td>
                                    </tr>
                                `;

                                $(`#permissionsTable tbody tr button[data-id="${id}"]`).closest('tr').fadeOut(400, function() {
                                    $(this).replaceWith(updatedRow);
                                    $(`#permissionsTable tbody tr button[data-id="${id}"]`).closest('tr').hide().fadeIn(400);
                                });

                            } else {
                                // Get the current number of rows
                                const currentRowCount = $('#permissionsTable tbody tr').length;

                                // Create new row with animation
                                const newRow = `
                                <tr style="display: none">
                                    <td>${currentRowCount + 1}</td>
                                    <td>${name}</td>
                                    <td>${description}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-permission" data-id="${rd.data}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-permission" data-id="${rd.data}">Delete</button>
                                    </td>
                                </tr>
                            `;

                                // Append and fade in the new row
                                $('#permissionsTable tbody').append(newRow);
                                $('#permissionsTable tbody tr:last').fadeIn(400);
                            }
                        });
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
                error: function(xhr, status, error) {
                    _thisButton.prop("disabled", false);
                    _thisButton.html(_thisButtonHTML);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


        // Reset form when modal is closed
        $('#permissionModal').on('hidden.bs.modal', function() {
            $('#permissionForm')[0].reset();
            $('#permissionId').val('');
        })
    });
</script>