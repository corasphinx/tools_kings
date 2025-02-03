$(function () {
    fetchReports();

    function fetchReports() {
        $('#reportsTable tbody').append(`
            <tr>
                <td colspan="6">
                    <div class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                    </div>
                </td>
            </tr>
        `);
        $.ajax({
            url: window.location.protocol + "//" + window.location.hostname + `/controllers/Report/fetch_all.php`,
            data: {},
            type: "POST",
            dataType: "text",
            success: function (rd) {
                rd = JSON.parse(rd);
                if (rd.success) {
                    renderReportsTable(rd.data);
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

    // Click event for the upload button
    $('#uploadBtn').click(function () {
        $('#fileInput').click();
    });

    $('#fileInput').change(function (event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file extension
            if (!file.name.toLowerCase().endsWith('.txt')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a .txt file'
                });
                $(this).val('');
                return;
            }

            uploadFile(file);
        }
    });

    function uploadFile(file) {
        $('#uploadBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');

        const reader = new FileReader();

        // Add time conversion function
        function convertTo24Hour(timeStr, lastModifier) {
            // Remove any extra spaces and convert to lowercase
            timeStr = timeStr.trim().toLowerCase();

            let hours, minutes, newModifier = lastModifier;

            // Check if time contains AM/PM
            if (timeStr.includes('am') || timeStr.includes('pm')) {
                // Extract hours and minutes, and AM/PM
                const timeComponents = timeStr.match(/(\d{1,2}):(\d{2})\s*(am|pm)/);
                if (!timeComponents) return { time: timeStr, modifier: lastModifier };

                hours = parseInt(timeComponents[1]);
                minutes = parseInt(timeComponents[2]);
                newModifier = timeComponents[3];
            } else {
                // No AM/PM specified - use last known modifier
                const timeComponents = timeStr.match(/(\d{1,2}):(\d{2})/);
                if (!timeComponents) return { time: timeStr, modifier: lastModifier };

                hours = parseInt(timeComponents[1]);
                minutes = parseInt(timeComponents[2]);
            }

            // Convert to 24-hour based on modifier
            if (newModifier === 'pm' && hours !== 12) {
                hours += 12;
            } else if (newModifier === 'am' && hours === 12) {
                hours = 0;
            }

            // Ensure hours are within valid range (0-23)
            hours = hours % 24;

            // Format with leading zeros
            return {
                time: `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`,
                modifier: newModifier
            };
        }

        reader.onload = function (e) {
            const content = e.target.result;
            const lines = content.split('\n').filter(line => line.trim());

            const parsedData = [];
            let currentTime = '';
            let lastModifier = null; // Track last AM/PM modifier

            // Define action verbs and their patterns
            const actionPatterns = [
                {
                    verb: 'attached',
                    regex: /\[(.*?)\]\s+(.*?)\s+attached\s+(.*)/i,
                    action: 'Attached'
                },
                {
                    verb: 'commented',
                    regex: /\[(.*?)\]\s+(.*?)\s+commented\s+(.*)/i,
                    action: 'Commented'
                },
                {
                    verb: 'moved',
                    regex: /\[(.*?)\]\s+(.*?)\s+moved\s+(.*)/i,
                    action: 'Moved'
                },
                {
                    verb: 'removed',
                    regex: /\[(.*?)\]\s+(.*?)\s+removed\s+(.*)/i,
                    action: 'Removed'
                },
                {
                    verb: 'changed',
                    regex: /\[(.*?)\]\s+(.*?)\s+changed\s+(.*)/i,
                    action: 'Changed'
                },
                {
                    verb: 'updated',
                    regex: /\[(.*?)\]\s+(.*?)\s+updated\s+(.*)/i,
                    action: 'Updated'
                },
                {
                    verb: 'closed',
                    regex: /\[(.*?)\]\s+(.*?)\s+closed\s+(.*)/i,
                    action: 'Closed'
                },
                {
                    verb: 'created',
                    regex: /\[(.*?)\]\s+(.*?)\s+created\s+(.*)/i,
                    action: 'Created'
                }
            ];

            lines.forEach((line, index) => {
                if (line === 'incoming-webhook') return;

                // Check if it's a timestamp line and convert to 24-hour format
                const timeMatch = line.match(/^(?:APP\s+)?(\d{1,2}:\d{2}(?:\s*[AP]M)?)/i);

                if (timeMatch) {
                    const result = convertTo24Hour(timeMatch[1], lastModifier);
                    currentTime = result.time;
                    lastModifier = result.modifier || lastModifier;
                    return;
                }

                // Find the first occurring action in the line
                let firstAction = null;
                let firstMatch = null;
                let earliestIndex = Infinity;

                // Check each pattern and find the one that occurs first in the line
                for (const pattern of actionPatterns) {
                    const match = line.match(pattern.regex);
                    if (match) {
                        // Find the actual position of the verb in the original line
                        const verbIndex = line.toLowerCase().indexOf(pattern.verb.toLowerCase());
                        if (verbIndex !== -1 && verbIndex < earliestIndex) {
                            earliestIndex = verbIndex;
                            firstAction = pattern;
                            firstMatch = match;
                        }
                    }
                }

                // If we found a match and have a current time, process it
                if (firstMatch && currentTime && firstAction) {
                    const project = firstMatch[1];
                    const employee = firstMatch[2].trim();
                    const description = firstMatch[3].trim();

                    parsedData.push({
                        time: currentTime,
                        project,
                        employee,
                        action: firstAction.action,
                        description
                    });
                }
            });


            // Sort data by time
            parsedData.sort((a, b) => {
                return new Date('2000/01/01 ' + a.time) - new Date('2000/01/01 ' + b.time);
            });

            const lineCount = parsedData.length;
            const projectCount = new Set(parsedData.map(item => item.project)).size;
            const employeeCount = new Set(parsedData.map(item => item.employee)).size;
            const actionCount = new Set(parsedData.map(item => item.action)).size;

            saveData(
                lineCount,
                projectCount,
                employeeCount,
                actionCount,
                parsedData
            );
        };

        reader.onerror = function (error) {
            console.error('File reading error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Reading Error',
                text: 'Failed to read the file'
            });
        };

        reader.readAsText(file);
    }
})

function saveData(lineCount, projectCount, employeeCount, actionCount, parsedData) {
    const _thisButton = $('#uploadBtn');
    const _thisButtonHTML = '<i class="bi bi-arrow-up"></i> Upload Today TXT';

    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Report/save.php`,
        data: {
            lineCount, projectCount, employeeCount, actionCount, parsedData: JSON.stringify(parsedData)
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

function renderReportsTable(reports) {
    $('#reportsTable tbody').html('');
    if (reports) {
        reports.map((report, i) => {
            $('#reportsTable tbody').append(`
                <tr>
                    <td class="align-middle">
                        <p class="text-md font-weight-bold mb-0">${report.created_at.split(' ')[0]}</p>
                    </td>
                    <td>
                        <h6 class="mb-0 text-sm">${report.line_count}</h6>
                    </td>
                    <td>
                        <h6 class="mb-0 text-sm">${report.project_count}</h6>
                    </td>
                    <td>
                        <h6 class="mb-0 text-sm">${report.employee_count}</h6>
                    </td>
                    <td>
                        <h6 class="mb-0 text-sm">${report.action_count}</h6>
                    </td>
                    <td class="align-middle">
                        <a href="/report-detail?i=${report.id}" class="text-info font-weight-bold">
                            <i class="bi bi-info-circle"></i>
                        </a>
                    </td>
                </tr>
            `);
        })
    }
    new simpleDatatables.DataTable($('#reportsTable')[0]);
}