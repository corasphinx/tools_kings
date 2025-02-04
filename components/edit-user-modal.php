<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x text-primary"></i></button>
            </div>
            <div class="modal-body">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" id="userID" />
                                <div class="row">
                                    <!-- User Information Section -->
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label text-bos">First Name</label>
                                            <input type="text" class="form-control text-bos" id="first_name" name="first_name" value="">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label text-bos">Last Name</label>
                                            <input type="text" class="form-control text-bos" id="last_name" name="last_name" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="email" class="form-label text-bos">Email</label>
                                            <input type="text" class="form-control text-bos" id="email" name="email" value="">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="password1" class="form-label text-bos">New Password</label>
                                            <input type="password" class="form-control text-bos" id="password1" name="password1" value="">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="password2" class="form-label text-bos">New Password Confirm</label>
                                            <input type="password" class="form-control text-bos" id="password2" name="password2" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm me-2 btn-success" id="saveButton"><i class="bi bi-floppy me-1"></i> Save</button>
                <button type="button" class="btn btn-sm me-2 btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x me-1"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#editUserModal #saveButton').click(function() {
            const userID = $('#editUserModal #userID').val();

            if (!userID)
                createUser();
            else
                updateUser(userID);
        })

        function createUser() {
            const isUserInfoValid = validateUserInfo();
            const isPasswordValid = validatePassword("create");

            if (isUserInfoValid && isPasswordValid) {

                saveUser();
            }
        }

        function updateUser(userID) {
            const isUserInfoValid = validateUserInfo();
            const isPasswordValid = validatePassword("update");

            if (isUserInfoValid && isPasswordValid) {
                saveUser(userID);
            }
        }
        // Real-time email validation
        $('#email').on('input', function() {
            const input = $(this);
            const value = input.val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            input.removeClass('is-invalid');
            input.siblings('.invalid-feedback').remove();

            if (value && !emailRegex.test(value)) {
                showError(input, 'Please enter a valid email address');
            }
        });

        // Real-time validation
        $('#first_name').on('input', function() {
            if (!$(this).val()) {
                showError($(this), 'This field is required');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
        // Real-time validation
        $('#last_name').on('input', function() {
            if (!$(this).val()) {
                showError($(this), 'This field is required');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });

        // Real-time password matching validation
        $('#password2').on('input', function() {
            const password1 = $('#password1').val();
            const password2 = $(this).val();

            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();

            if (password1 && password2 && password1 !== password2) {
                showError($(this), 'Passwords do not match');
            }
        });
    })

    // Part 1: User Info Validation
    function validateUserInfo() {
        let isValid = true;

        // Reset previous error states for user info fields
        $('#first_name, #last_name, #email').removeClass('is-invalid');
        $('#first_name, #last_name, #email').siblings('.invalid-feedback').remove();

        // Required fields validation for user info
        ['first_name', 'last_name', 'email'].forEach(field => {
            const input = $(`#${field}`);
            const value = input.val().trim();

            if (!value) {
                isValid = false;
                showError(input, 'This field is required');
            }
        });

        // Email format validation
        const emailInput = $('#email');
        const emailValue = emailInput.val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailValue && !emailRegex.test(emailValue)) {
            isValid = false;
            showError(emailInput, 'Please enter a valid email address');
        }

        return isValid;
    }

    // Part 2: Password Validation
    function validatePassword(type) {
        let isValid = true;

        // Reset previous error states for password fields
        $('#password1, #password2').removeClass('is-invalid');
        $('#password1, #password2').siblings('.invalid-feedback').remove();

        const password1 = $('#password1').val();
        const password2 = $('#password2').val();

        if (type == 'create') {
            // Required fields validation for passwords
            if (!password1) {
                isValid = false;
                showError($('#password1'), 'Password is required');
            }

            if (!password2) {
                isValid = false;
                showError($('#password2'), 'Password confirmation is required');
            }
            // Password matching validation
            if (password1 && password2 && password1 !== password2) {
                isValid = false;
                showError($('#password2'), 'Passwords do not match');
            }
        } else {
            // Password matching validation
            if ((password1 || password2) && password1 !== password2) {
                isValid = false;
                showError($('#password2'), 'Passwords do not match');
            }
        }

        return isValid;
    }

    // Helper function to show error messages
    function showError(input, message) {
        input.addClass('is-invalid');
        input.after(`<div class="invalid-feedback">${message}</div>`);
    }

    function saveUser(userID = 0) {
        // Change button text and disable it
        const _thisButton = $('#editUserModal #saveButton');
        const _thisButtonHTML = _thisButton.text();
        _thisButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/User/save.php`,
            data: {
                id: userID,
                first_name: $('#editUserModal #first_name').val(),
                last_name: $('#editUserModal #last_name').val(),
                email: $('#editUserModal #email').val(),
                password: $('#editUserModal #password1').val(),
            },
            type: "POST",
            dataType: "text",
            success: function(rd) {
                rd = JSON.parse(rd);
                _thisButton.prop("disabled", false);
                _thisButton.html(_thisButtonHTML);
                if (rd.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: rd.message,
                        timer: 1500
                    }).then(() => {
                        location.reload();
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
            error: function(error) {
                _thisButton.prop("disabled", false);
                _thisButton.html(_thisButtonHTML);
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
</script>