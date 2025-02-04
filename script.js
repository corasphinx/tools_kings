$(document).ready(function () {
    // Function to render existing PTO data
    function renderPtoData(ptoData) {
        // Clear existing tbody content first
        $('#ptoTable tbody').empty();

        // Render each PTO record
        ptoData.forEach((pto, index) => {
            const timeOff = getTimeOffString(pto.time_off);
            const newRow = `
                <tr>
                    <td class="align-middle ps-4">
                        <p class="text-secondary text-xs font-weight-bold mb-0">${index + 1}</p>
                    </td>
                    <td>
                        <p class="font-weight-bold mb-0">${pto.amount}</p>
                    </td>
                    <td>
                        <p class="mb-0">${timeOff}</p>
                    </td>
                </tr>
            `;
            $('#ptoTable tbody').append(newRow);
        });
    }

    loadPTO();

    function loadPTO() {
        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/PTO/fetch_all.php`,
            data: {
                user_id: LOGGED_IN_USER_ID
            },
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    renderPtoData(rd.data);
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
            }
        })
    }
})

function getTimeOffString(timeOff) {
    switch (timeOff) {
        case 'annual':
            return 'Annual Leave'
        case 'sick':
            return 'Sick Leave'
        case 'personal':
            return 'Personal Leave'

        default:
            return '';
    }
}