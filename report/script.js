$(function () {
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

                // Try to match the line against each action pattern
                for (const pattern of actionPatterns) {
                    const match = line.match(pattern.regex);
                    if (match && currentTime) {
                        const project = match[1];
                        const employee = match[2].trim();
                        const description = match[3].trim();

                        parsedData.push({
                            time: currentTime,
                            project,
                            employee,
                            action: pattern.action,
                            description
                        });
                        break;
                    }
                }
            });

            // Sort data by time
            parsedData.sort((a, b) => {
                return new Date('2000/01/01 ' + a.time) - new Date('2000/01/01 ' + b.time);
            });

            // Create table HTML
            let tableHTML = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Project</th>
                            <th>Employee</th>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let i = 1;
            parsedData.forEach(item => {
                tableHTML += `
                    <tr>
                        <td>${i++}</td>
                        <td>${item.time}</td>
                        <td>${item.project}</td>
                        <td>${item.employee}</td>
                        <td>${item.action}</td>
                        <td>${item.description}</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            // Display table
            $('#resultContainer').html(tableHTML);

            // Success message
            Swal.fire({
                icon: 'success',
                title: 'File Parsed Successfully',
                text: `Processed ${parsedData.length} entries`
            });
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