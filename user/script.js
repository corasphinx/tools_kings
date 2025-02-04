$(document).ready(function () {
    const url = new URL(window.location.href);
    const ID = url.searchParams.get('i');

    loadRoles();
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
    function loadRoles() {

        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/Role/fetch_all.php`,
            data: {
            },
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    renderRoles(rd.data);
                    loadUserData();
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

    function renderRoles(roles) {
        $('#role').empty();

        if (roles) {
            roles.map(role => {
                $('#role').append(`<option value="${role.id}">${role.name}</option>`);
            })

        } else {
            $('#role').append(`<option value="">No roles available</option>`);
        }
    }
    function renderUserData(user) {

        $('#firstName').val(user.first_name);
        $('#lastName').val(user.last_name);
        $('#role').val(user.role_id);
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
        renderDocuments(user.documents);

    };

    const uploadArea = $('#uploadArea');
    const fileInput = $('#fileInput');
    const fileList = $('#fileList');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.on(eventName, preventDefaults);
        $(document).on(eventName, preventDefaults);
    });

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.on(eventName, highlight);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.on(eventName, unhighlight);
    });

    // Handle dropped files
    uploadArea.on('drop', handleDrop);
    fileInput.on('change', handleFiles);

    // Make the entire upload area clickable
    uploadArea.on('click', function (e) {
        if (e.target.tagName !== 'INPUT') {
            fileInput.click();
        }
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        uploadArea.addClass('dragover');
    }

    function unhighlight(e) {
        uploadArea.removeClass('dragover');
    }

    function handleDrop(e) {
        const dt = e.originalEvent.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
    }

    function handleFiles(e) {
        const files = [...e.target.files];
        files.forEach(uploadFile);
    }

    function uploadFile(file) {
        const fileId = Date.now();

        // Create file item HTML with Bootstrap icons
        const fileItem = $(`
            <div class="file-item" id="file-${fileId}">
                <i class="bi bi-file-earmark me-2"></i>
                <div class="file-name">${file.name}</div>
                <div class="progress w-25 me-2">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <button class="btn btn-danger btn-sm">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        `);

        fileList.append(fileItem);

        // Create FormData object
        const formData = new FormData();
        formData.append('file', file);
        formData.append('user_id', ID);

        // Store the ajax request reference for cancellation
        let ajaxRequest = $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/User/upload_document.php`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        fileItem.find('.progress-bar').css('width', percentComplete + '%');
                    }
                });
                return xhr;
            },
            success: function (response) {
                // Handle successful upload
                fileItem.find('.progress').remove();
                fileItem.find('.btn-danger')
                    .removeClass('btn-danger')
                    .addClass('btn-success')
                    .html('<i class="bi bi-check-circle"></i>')
                    .prop('disabled', true);

            },
            error: function (xhr, status, error) {
                // Handle upload error
                fileItem.find('.progress-bar')
                    .addClass('bg-danger');

                fileItem.append(`
                    <div class="text-danger ms-2">
                        <i class="bi bi-exclamation-circle"></i> 
                        Upload failed: ${error}
                    </div>
                `);

                toastr.error('Upload failed:', error);
            }
        });

        // Handle cancel button
        fileItem.find('.btn-danger').click(function () {
            // Abort the AJAX request if it's still ongoing
            if (ajaxRequest && ajaxRequest.state() === 'pending') {
                ajaxRequest.abort();
            }
            fileItem.remove();
        });
    }

    function renderDocuments(documents) {
        $('#documentsContainer').html('');
        if (documents) {
            documents.map(doc => {
                $('#documentsContainer').append(`
                    <div class="file-item" id="file-${doc.id}">
                        <i class="bi bi-file-earmark me-2"></i>
                        <div class="file-name">${doc.origin_name}</div>
                        <a class="btn btn-info btn-sm mb-0 text-white" href="${doc.path}" target="_blank">
                            <i class="bi bi-arrow-up-right-square"></i>
                        </a>
                    </div>
                `);
            });
        }
    }

    $("#saveButton").on("click", function (e) {
        e.preventDefault();

        const _thisButton = $('#saveButton');
        const _thisButtonHTML = _thisButton.text();
        _thisButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        let formData = new FormData($('#profileForm')[0]);
        formData.append('id', ID);

        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/User/update.php`,
            data: formData,
            type: "POST",
            processData: false,
            contentType: false,
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
    })
})

