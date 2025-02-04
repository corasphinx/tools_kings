<!-- FullCalendar CSS -->
<link href='/assets/vendor/fullcalendar@5.11.3/main.css' rel='stylesheet' />
<style>
    .calendar-wrapper {
        padding: 10px;
    }

    /* Custom calendar styles */
    .fc-theme-standard td {
        border: 1px solid #ddd;
    }

    .fc-day-today {
        background: #f8f9fa !important;
    }

    .fc-header-toolbar {
        margin-bottom: 0.5em !important;
    }

    .fc-event {
        border: none;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.85em;
    }

    .current-month {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
</style>
<?php require_once("{$_SERVER['DOCUMENT_ROOT']}/components/edit-event-modal.php"); ?>
<script src='/assets/vendor/fullcalendar@5.11.3/main.js'></script>
<script>
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
            events: [],

            // Calendar configurations
            firstDay: 1, // Monday as first day
            businessHours: true,
            selectable: false,
            selectHelper: true,

            // Event handling
            eventDidMount: function(info) {
                // Create tooltip content combining title and description
                let tooltipContent = info.event.title;
                if (info.event.extendedProps.description) {
                    tooltipContent += '<br><small>' + info.event.extendedProps.description + '</small>';
                    tooltipContent += '<br><small>' + info.event.extendedProps.start_at + '~' + info.event.extendedProps.end_at + '</small>';
                }

                $(info.el).tooltip({
                    title: tooltipContent,
                    html: true, // Enable HTML in tooltip
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },

            dateClick: function(info) {
                $('#eventStartAt').val('00:00');
                $('#eventEndAt').val('23:59');
                $('#eventDate').val(info.dateStr);
                $('#editEventModal').modal('show');
            },

            eventClick: function(info) {
                console.log
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

        const $eventModal = $('#editEventModal');
        const $form = $('#editEventForm');

        // Handle save button click
        $('#saveEvent').on('click', function() {
            if ($form[0].checkValidity()) {
                const event = {
                    id: $('#eventId').val(),
                    date: $('#eventDate').val(),
                    subject: $('#eventSubject').val(),
                    start_at: $('#eventStartAt').val(),
                    end_at: $('#eventEndAt').val(),
                    status: $('#eventStatus').val(),
                    description: $('#eventDescription').val(),
                    is_global: $('#eventIsGlobal').is(':checked') ? 1 : 0,
                    assigneees: $('#assigneees').val(),
                    created_by: selectedCalendarUserId
                };

                // Show loading state
                $('#saveEvent').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                // If eventId is empty, it's a new event
                if (!event.id) {
                    createEvent(event);
                } else {
                    // updateEvent(event);
                }


            } else {
                $form[0].reportValidity();
            }
        });

        // Reset form when modal is closed
        $eventModal.on('hidden.bs.modal', function() {
            $form[0].reset();
            $('#eventId').val('');
            $('#editEventModalLabel').text('Create New Event');
            $('#eventIsGlobal').prop('checked', false);
            $('#saveEvent').prop('disabled', false).html('Save Event');
            if ($('#assigneees').length) {
                $('#assigneees').val(null).trigger('change');
            }
        });

        loadEvents();

        function loadEvents() {
            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Event/fetch_by_user_id.php`,
                data: {
                    user_id: selectedCalendarUserId
                },
                type: "POST",
                dataType: "text",
                success: function(rd) {
                    rd = JSON.parse(rd);
                    if (rd.success) {
                        if (rd.data) {
                            rd.data.map(event => {
                                calendar.addEvent({
                                    title: event.subject,
                                    start: event.date,
                                    end: event.date,
                                    extendedProps: {
                                        start_at: event.start_at,
                                        end_at: event.end_at,
                                        description: event.description
                                    },
                                    backgroundColor: event.status == 'not_available' ? '#f44336' : '#4caf50'
                                });
                            })
                        }
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                },
            })
        }

        // Function to create new event
        function createEvent(event) {
            $.ajax({
                url: window.location.protocol + "//" + window.location.hostname + `/controllers/Event/create.php`,
                data: event,
                type: "POST",
                dataType: "text",
                success: function(rd) {
                    rd = JSON.parse(rd);
                    if (rd.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: rd.message,
                            timer: 1500
                        })
                        // Add event to calendar
                        if ($('#assigneees').length && (
                                $('#assigneees').val().filter(id => id == LOGGED_IN_USER_ID).length > 0 || !$('#assigneees').val().length
                            ) || !$('#assigneees').length) {
                            calendar.addEvent({
                                title: event.subject,
                                start: event.date,
                                end: event.date,
                                extendedProps: {
                                    start_at: event.start_at,
                                    end_at: event.end_at,
                                    description: event.description
                                },
                                backgroundColor: event.status == 'not_available' ? '#f44336' : '#4caf50'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: rd.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Close modal
                    $eventModal.modal('hide');

                    // Reset form
                    $form[0].reset();
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                },
            })

        }

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(e.target).attr('id') === 'calendar-tab') {
                calendar.render();
            }
        });
    });
</script>