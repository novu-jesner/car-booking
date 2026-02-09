<!-- Booking Modal -->
<div class="modal fade" id="finishModal" tabindex="-1" aria-labelledby="bookingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bookingLabel">Finish my ride</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="myForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="odo_meter" class="form-label">Current Odo meter</label>
                        <input type="text" name="odo_meter" class="form-control form-control-sm" id="odo_meter" required>
                        <span id="odo_meter_error" class="text-danger d-none"></span>
                    </div>
                
                    <!-- Image Attachment -->
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (JPEG, PNG, HEIC)</label>
                        <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" accept=".jpeg,.jpg,.png,.heic" required>
                        <span id="attachment_error" class="text-danger d-none"></span>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button id="submit-button" type="submit" class="btn btn-primary">Finish Ride</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
