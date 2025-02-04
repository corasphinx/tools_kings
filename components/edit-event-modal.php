<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Create New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEventForm">
                    <input type="hidden" id="eventId">
                    <input type="hidden" id="eventDate">
                    <div class="mb-3">
                        <label for="eventSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="eventSubject" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="eventStartAt" class="form-label">Start At</label>
                            <input type="time" class="form-control" id="eventStartAt" required>
                        </div>
                        <div class="col-md-6">
                            <label for="eventEndAt" class="form-label">End At</label>
                            <input type="time" class="form-control" id="eventEndAt" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventStatus" class="form-label">Status</label>
                        <select class="form-select" id="eventStatus" required>
                            <option value="">Select Status</option>
                            <option value="available" class="text-success">🟢 Available</option>
                            <option value="not_available" class="text-warning">🔴 Not Available</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"
                            placeholder="Enter event description..."></textarea>
                    </div>
                    <?php if (in_array("Global Calendar", $Account->permissions)) { ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="eventIsGlobal">
                                <label class="form-check-label" for="eventIsGlobal">
                                    Is Global
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEvent">Save Event</button>
            </div>
        </div>
    </div>
</div>