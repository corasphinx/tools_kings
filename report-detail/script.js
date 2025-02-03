$(function () {
    // Get current URL object
    const url = new URL(window.location.href);

    // Get single parameter
    const reportID = url.searchParams.get('i');

    fetchReportLines(reportID);
})
// Process the data by hour
function processHourlyData(data) {
    // Get unique employees
    const employees = [...new Set(data.map(item => item.employee))];
    
    // Group data by hour
    const hourlyData = data.reduce((acc, item) => {
        const hour = item.time.split(':')[0] + ':00'; // Convert "08:10" to "08:00"
        if (!acc[hour]) {
            acc[hour] = {};
            employees.forEach(emp => acc[hour][emp] = 0);
        }
        acc[hour][item.employee]++;
        return acc;
    }, {});

    // Sort hours
    const hours = Object.keys(hourlyData).sort();

    // Create datasets for each employee
    const datasets = employees.map(employee => {
        // Count activities per hour for this employee
        const counts = hours.map(hour => hourlyData[hour][employee]);

        // Generate random color for this line
        const color = `hsl(${Math.random() * 360}, 70%, 50%)`;

        return {
            label: employee,
            data: counts,
            borderColor: color,
            backgroundColor: color + '20',
            tension: 0.4,
            fill: false
        };
    });

    return { hours, datasets };
}

// Create the chart
function createHourlyChart(data) {
    const { hours, datasets } = processHourlyData(data);
    
    const ctx = document.getElementById('hourlyChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours,
            datasets: datasets
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Number of Activities per Hour'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hour'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Hourly Employee Activity'
                },
                tooltip: {
                    callbacks: {
                        title: (context) => `Hour: ${context[0].label}`,
                        label: (context) => {
                            return `${context.dataset.label}: ${context.parsed.y} activities`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    align: 'center'
                }
            }
        }
    });
}

// Process data for stacked column chart
function processProjectData(data) {
    // Get unique projects and employees
    const projects = [...new Set(data.map(item => item.project))];
    const employees = [...new Set(data.map(item => item.employee))];
    
    // Count activities per project per employee
    const projectData = projects.reduce((acc, project) => {
        acc[project] = {};
        employees.forEach(emp => {
            acc[project][emp] = data.filter(item => 
                item.project === project && 
                item.employee === emp
            ).length;
        });
        return acc;
    }, {});

    // Create datasets for each employee
    const datasets = employees.map(employee => {
        // Generate random color for this employee
        const color = `hsl(${Math.random() * 360}, 70%, 50%)`;

        return {
            label: employee,
            data: projects.map(project => projectData[project][employee]),
            backgroundColor: color,
            borderColor: 'white',
            borderWidth: 1
        };
    });

    return { projects, datasets };
}

// Create the stacked column chart
function createProjectChart(data) {
    const { projects, datasets } = processProjectData(data);
    
    const ctx = document.getElementById('projectChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: projects,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Projects'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Number of Activities'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Employee Activities by Project'
                },
                tooltip: {
                    callbacks: {
                        title: (context) => `Project: ${context[0].label}`,
                        label: (context) => {
                            return `${context.dataset.label}: ${context.parsed.y} activities`;
                        },
                        footer: (tooltipItems) => {
                            const total = tooltipItems.reduce((sum, ti) => sum + ti.parsed.y, 0);
                            return `Total: ${total} activities`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    align: 'center'
                }
            }
        }
    });
}

function fetchReportLines(reportID){
    if(!reportID){
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Invalid Report',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return;
    }
    $('#resultContainer tbody').append(`
        <tr>
            <td colspan="6">
                <div class="text-center">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                </div>
            </td>
        </tr>
    `);
    $.ajax({
        url: window.location.protocol + "//" + window.location.hostname + `/controllers/Report/fetch_lines.php`,
        data: {
            report_id: reportID
        },
        type: "POST",
        dataType: "text",
        success: function (rd) {
          rd = JSON.parse(rd);
          if (rd.success) {
            renderTable(rd.data.lines);
            createHourlyChart(rd.data.lines);
            createProjectChart(rd.data.lines);
            renderHeaderCards(rd.data.reportInfo);
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

function renderTable(data){
    let tbodyHTML = '';
    if(data){
        let i = 1;
        data.forEach(item => {
            tbodyHTML += `
                <tr>
                    <td>${i++}</td>
                    <td>${item.time}</td>
                    <td>${item.project}</td>
                    <td>${item.employee}</td>
                    <td>${item.action}</td>
                    <td><span style="text-wrap:auto">${item.description}<span></td>
                </tr>
            `;
        });
    }
    // Display table
    $('#resultContainer tbody').html(tbodyHTML);
    new simpleDatatables.DataTable($('#resultContainer')[0]);
}

function renderHeaderCards(reportInfo){
    const formatter = new Intl.NumberFormat();
    $('#totalEntryCount').html(formatter.format(reportInfo.line_count));
    $('#totalEmployeeCount').html(formatter.format(reportInfo.employee_count));
    $('#totalProjectCount').html(formatter.format(reportInfo.project_count));
    $('#totalActionCount').html(formatter.format(reportInfo.action_count));
}