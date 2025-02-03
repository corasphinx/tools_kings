$(document).ready(function () {
    // Email validation regex pattern
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    // Function to validate email
    function validateEmail(email) {
        return emailPattern.test(email);
    }

    // Function to show error
    function showError(element, message) {
        element.addClass('is-invalid');
        element.next('.invalid-feedback').text(message);
    }

    // Function to clear error
    function clearError(element) {
        element.removeClass('is-invalid');
    }

    // Real-time validation for email
    $('#email').on('input', function () {
        const email = $(this).val().trim();
        if (email === '') {
            showError($(this), 'Email is required');
        } else if (!validateEmail(email)) {
            showError($(this), 'Please enter a valid email address');
        } else {
            clearError($(this));
        }
    });

    // Real-time validation for password
    $('#password').on('input', function () {
        const password = $(this).val().trim();
        if (password === '') {
            showError($(this), 'Password is required');
        } else {
            clearError($(this));
        }
    });

    // Form submission
    $('#signinButton').click(function () {
        let isValid = true;
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();

        // Validate email
        if (email === '') {
            showError($('#email'), 'Email is required');
            isValid = false;
        } else if (!validateEmail(email)) {
            showError($('#email'), 'Please enter a valid email address');
            isValid = false;
        }

        // Validate password
        if (password === '') {
            showError($('#password'), 'Password is required');
            isValid = false;
        }

        // If validation passes
        if (isValid) {
            // Change button text and disable it
            const _thisButton = $('#signinButton');
            const _thisButtonHTML = _thisButton.text();
            _thisButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...');
            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Auth/login.php`,
                data: {
                  email,
                  password
                },
                type: "POST",
                dataType: "text",
                success: function (rd) {
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
                        location.href = rd.data.redirect;
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
                error: function (error) {
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
    });
});