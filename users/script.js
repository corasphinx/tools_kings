$(function () {
    fetchUsers();

    function fetchUsers() {
        $('#usersTable tbody').append(`
            <tr>
                <td colspan="4">
                    <div class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                    </div>
                </td>
            </tr>
        `);
        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/User/fetch_all.php`,
            data: {},
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    renderUsersTable(rd.data);
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
                    text: error.responseText,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            },
        })
    }

    $('#createUserButton').click(function () {
        $('#editUserModal .modal-title').html('Create New User');
        $('#editUserModal input').val('');
        $('#editUserModal').modal('toggle');
    })
})

function renderUsersTable(users) {
    $('#usersTable tbody').html('');
    users.map((user, i) => {
        $('#usersTable tbody').append(`
            <tr>
                <td class="align-middle">
                    <p class="text-md font-weight-bold mb-0">${i + 1}</p>
                </td>
                <td>
                    <h6 class="mb-0 text-sm">${user.first_name} ${user.last_name}</h6>
                </td>
                <td>
                    <p class="text-xs font-weight-bold mb-0">${user.email}</p>
                </td>
                <td class="align-middle">
                    <a href="/user?i=${user.id}" class="text-info font-weight-bold user-info" user-id="${user.id}">
                        <i class="bi bi-info-circle"></i>
                    </a>
                </td>
            </tr>
        `);
    })


    new simpleDatatables.DataTable($('#usersTable')[0]);
}