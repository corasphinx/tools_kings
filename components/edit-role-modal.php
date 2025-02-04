<!-- Role Modal -->
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Optional: Select2 Bootstrap 5 Theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <select class="form-select" id="rolePermissions" multiple>
                            <!-- Permissions will be dynamically loaded here -->
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple permissions</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRole">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#rolePermissions').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select permissions',
            width: '100%',
            closeOnSelect: false
        });

        // Load permissions into dropdown when document is ready
        loadPermissionsDropdown();

        // Function to load permissions
        function loadPermissionsDropdown() {
            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Permission/fetch_all.php`,
                type: 'GET',
                success: function(response) {
                    const permissions = JSON.parse(response);
                    const select = $('#rolePermissions');
                    select.empty();

                    if (permissions.success) {
                        permissions.data.forEach(permission => {
                            select.append(new Option(permission.name, permission.id));
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load permissions',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
        $('#saveRole').click(function() {
            const id = $('#roleId').val();
            const name = $('#roleName').val();
            const permissions = $('#rolePermissions').val();

            const _thisButton = $(this);
            const _thisButtonHTML = _thisButton.text();
            _thisButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Role/save.php`,
                type: 'POST',
                data: {
                    id,
                    name,
                    permissions
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
                            $('#roleModal').modal('hide');

                            // Get selected permission names for display

                            const permissionBadges = $('#rolePermissions option:selected')
                                .map(function() {
                                    return `<span class="badge bg-info me-1 mb-1">${$(this).text()}</span>`
                                })
                                .get()
                                .join('');

                            if (id) {
                                // Update existing row
                                const updatedRow = `
                                    <tr>
                                        <td>${$(`#rolesTable tbody tr button[data-id="${id}"]`).closest('tr').find('td:first').text()}</td>
                                        <td>${name}</td>
                                        <td>${permissionBadges}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-role" data-id="${id}">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-role" data-id="${id}">Delete</button>
                                        </td>
                                    </tr>
                                `;

                                $(`#rolesTable tbody tr button[data-id="${id}"]`).closest('tr').fadeOut(400, function() {
                                    $(this).replaceWith(updatedRow);
                                    $(`#rolesTable tbody tr button[data-id="${id}"]`).closest('tr').hide().fadeIn(400);
                                });

                            } else {
                                // Get the current number of rows
                                const currentRowCount = $('#rolesTable tbody tr').length;

                                // Create new row with animation
                                const newRow = `
                                    <tr style="display: none">
                                        <td>${currentRowCount + 1}</td>
                                        <td>${name}</td>
                                        <td>${permissionBadges}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-role" data-id="${rd.data}">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-role" data-id="${rd.data}">Delete</button>
                                        </td>
                                    </tr>
                                `;

                                // Append and fade in the new row
                                $('#rolesTable tbody').append(newRow);
                                $('#rolesTable tbody tr:last').fadeIn(400);
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
        $('#roleModal').on('hidden.bs.modal', function() {
            $('#roleForm')[0].reset();
            $('#roleId').val('');
            $('#rolePermissions').val(null).trigger('change');
        })
    });
</script>