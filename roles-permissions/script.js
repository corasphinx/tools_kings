$(document).ready(function () {
    fetchRoles();
    fetchPermissions();
    // Event handlers
    $('#addRoleBtn').click(function () {
        // Show role modal
        showRoleModal();
    });

    $('#addPermissionBtn').click(function () {
        // Show permission modal
        showPermissionModal();
    });

    // Handle role actions
    $(document).on('click', '.edit-role', function () {
        const roleId = $(this).data('id');
        editRole(roleId);
    });

    $(document).on('click', '.delete-role', function () {
        const roleId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Call delete function if user confirms
                deleteRole(roleId);
            }
        });
    });

    // Handle permission actions
    $(document).on('click', '.edit-permission', function () {
        const permissionId = $(this).data('id');
        editPermission(permissionId);
    });

    $(document).on('click', '.delete-permission', function () {
        const permissionId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Call delete function if user confirms
                deletePermission(permissionId);
            }
        });
    });
});

function fetchPermissions() {
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Permission/fetch_all.php`,
        method: 'GET',
        success: function (response) {
            const permissions = JSON.parse(response).data;
            $('#permissionsTable tbody').empty();
            if (permissions)
                permissions.map((permission, idx) => {
                    const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${permission.name}</td>
                        <td>${permission.description}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-permission" data-id="${permission.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-permission" data-id="${permission.id}">Delete</button>
                        </td>
                    </tr>
                `
                    $('#permissionsTable tbody').append(row);
                });
        },
        error: function (error) {
            console.log(error);
        }
    })
}

function deletePermission(permissionId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Permission/delete_by_id.php?id=${permissionId}`,
        method: 'GET',
        success: function (response) {
            Swal.fire({
                title: 'Deleted!',
                text: 'Permission has been deleted.',
                icon: 'success'
            }).then(() => {
                // Refresh the table after deletion
                // Fade out the deleted row
                $(`#permissionsTable button[data-id="${permissionId}"]`).closest('tr').fadeOut(400, function () {
                    // Remove the row after fade out
                    $(this).remove();

                    // Reorder index numbers with animation
                    $('#permissionsTable tbody tr').each(function (idx) {
                        const cell = $(this).find('td:first');
                        const currentNum = parseInt(cell.text());
                        const newNum = idx + 1;

                        if (currentNum !== newNum) {
                            cell.fadeOut(200, function () {
                                $(this).text(newNum).fadeIn(200);
                            });
                        }
                    });
                });
            });
        },
        error: function (error) {
            Swal.fire(
                'Error!',
                'Failed to delete permission.',
                'error'
            );
        }
    })
}

function editPermission(permissionId) {
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Permission/fetch_by_id.php?id=${permissionId}`,
        method: 'GET',
        success: function (response) {
            const permission = JSON.parse(response).data;
            showPermissionModal(permission);
        },
        error: function (error) {
            console.log(error);
        }
    })
}

function showPermissionModal(permissionData = null) {
    const modal = new bootstrap.Modal('#permissionModal');
    if (permissionData) {
        $('#permissionId').val(permissionData.id);
        $('#permissionName').val(permissionData.name);
        $('#permissionDescription').val(permissionData.description);
    } else {
        $('#permissionForm')[0].reset();
        $('#permissionId').val('');
    }
    modal.show();
}

// Role Controllers
function fetchRoles() {
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Role/fetch_all.php`,
        method: 'GET',
        success: function (response) {
            const roles = JSON.parse(response).data;
            $('#rolesTable tbody').empty();
            if (roles)
                roles.map((role, idx) => {
                    let permissionBadges = '';
                    if (role.permissions) {
                        permissionBadges = role.permissions
                            .map(permission => `<span class="badge bg-info me-1 mb-1">${permission.name}</span>`)
                            .join('');
                    }
                    const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${role.name}</td>
                        <td class="permissions-cell">${permissionBadges}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-role" data-id="${role.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-role" data-id="${role.id}">Delete</button>
                        </td>
                    </tr>
                `
                    $('#rolesTable tbody').append(row);
                });
        },
        error: function (error) {
            console.log(error);
        }
    })
}


function deleteRole(roleId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Role/delete_by_id.php?id=${roleId}`,
        method: 'GET',
        success: function (response) {
            Swal.fire({
                title: 'Deleted!',
                text: 'Role has been deleted.',
                icon: 'success'
            }).then(() => {
                // Refresh the table after deletion
                // Fade out the deleted row
                $(`#rolesTable button[data-id="${roleId}"]`).closest('tr').fadeOut(400, function () {
                    // Remove the row after fade out
                    $(this).remove();

                    // Reorder index numbers with animation
                    $('#rolesTable tbody tr').each(function (idx) {
                        const cell = $(this).find('td:first');
                        const currentNum = parseInt(cell.text());
                        const newNum = idx + 1;

                        if (currentNum !== newNum) {
                            cell.fadeOut(200, function () {
                                $(this).text(newNum).fadeIn(200);
                            });
                        }
                    });
                });
            });
        },
        error: function (error) {
            Swal.fire(
                'Error!',
                'Failed to delete role.',
                'error'
            );
        }
    })
}

function editRole(roleId) {
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Role/fetch_by_id.php?id=${roleId}`,
        method: 'GET',
        success: function (response) {
            const role = JSON.parse(response).data;
            showRoleModal(role);
        },
        error: function (error) {
            console.log(error);
        }
    })
}

function showRoleModal(roleData = null) {
    const modal = new bootstrap.Modal('#roleModal');
    if (roleData) {
        $('#roleId').val(roleData.id);
        $('#roleName').val(roleData.name);
        if (roleData.permissions) {
            $('#rolePermissions').val(roleData.permissions.map(p => p.permission_id)).trigger('change');
        }
    } else {
        $('#roleForm')[0].reset();
        $('#roleId').val('');
    }
    modal.show();
}