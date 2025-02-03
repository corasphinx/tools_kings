$(document).ready(function() {
    var calendarEl = $('#calendar')[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: false,
        weekNumbers: true,
        dayMaxEvents: true,
        events: [
            {
                title: 'Meeting',
                start: '2025-01-25',
                backgroundColor: '#3788d8'
            },
            {
                title: 'Holiday',
                start: '2025-01-26',
                end: '2025-01-28',
                backgroundColor: '#28a745'
            },
            {
                title: 'Conference',
                start: '2025-01-27',
                backgroundColor: '#dc3545'
            },
            {
                title: 'Team Meeting',
                start: '2025-01-28T10:30:00',
                end: '2025-01-28T12:30:00',
                backgroundColor: '#ffc107'
            }
        ],
        
        // Calendar configurations
        firstDay: 1, // Monday as first day
        businessHours: true,
        selectable: false,
        selectHelper: true,
        
        // Event handling
        eventDidMount: function(info) {
            $(info.el).tooltip({
                title: info.event.title,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        
        dateClick: function(info) {
            console.log('Clicked on: ' + info.dateStr);
        },
        
        eventClick: function(info) {
            console.log('Event: ' + info.event.title);
        }
    });
    
    calendar.render();

    // Function to add new event
    window.addEvent = function(eventData) {
        calendar.addEvent(eventData);
    };

    // Function to update calendar
    window.updateCalendar = function() {
        calendar.refetchEvents();
    };
});