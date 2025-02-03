$(function () {
    const url = new URL(window.location.href);
    const ID = url.searchParams.get('i');

    loadUserData();
    function loadUserData() {

        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/User/info.php`,
            data: {
                id: ID
            },
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    renderUserData(rd.data);
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

    function renderUserData(user) {

        $('#firstName').val(user.first_name);
        $('#lastName').val(user.last_name);
        $('#email').val(user.email);
        $('#phone').val(user.phone);
        $('#address').val(user.address);
        $('#city').val(user.city);
        $('#state').val(user.state);
        $('#zipCode').val(user.zip_code);
        $('#birthDate').val(user.birth_date);
        $('#ptoStartDate').val(user.pto_start_date);
        $('#ptoEndDate').val(user.pto_end_date);
        $('#bio').val(user.bio);

    };
})
