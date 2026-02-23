<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bookingLabel">Book a Car</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="myForm">
                @csrf
                <div class="modal-body">
                          <!-- Conflict warning will be injected here by JS -->
        <div id="conflict-warning" class="alert alert-warning d-none"></div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Description/Title</label>
                        <input type="text" name="title" class="form-control form-control-sm" id="title" required>
                        <span id="title_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose</label>
                        <textarea name="purpose" class="form-control form-control-sm" id="purpose" rows="3" required></textarea>
                        <span id="purpose_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="from_date" class="form-label">From Date & Time</label>
                        <input type="datetime-local" name="from_date" class="form-control form-control-sm" id="from_date" required>
                        <span id="from_date_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="to_date" class="form-label">To Date & Time</label>
                        <input type="datetime-local" name="to_date" class="form-control form-control-sm" id="to_date" required>
                        <span id="to_date_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="destination" class="form-label">Destination</label>
                        <input type="text" name="destination" class="form-control form-control-sm" id="destination" required>
                        <span id="destination_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Driver</label>
                        <select name="driver_id" class="form-select form-select-sm" id="driver_id" required>
                            <option value="" disabled selected>Select a driver</option>
                            <!-- Fetch drivers based on the 'driver' role using Spatie's role method -->
                            @foreach(\App\Models\User::role('driver')->where('isActive', true)->get() as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach                        
                        </select>
                        <span id="driver_id_error" class="text-danger d-none"></span>
                    </div>                    
                    <div class="mb-3">
                        <label for="car_id" class="form-label">Car</label>
                        <select name="car_id" class="form-select form-select-sm" id="car_id" required>
                            <option value="" disabled selected>Select a car</option>
                            <!-- Assuming cars are fetched from the 'cars' table -->
                            @foreach(\App\Models\Cars::all() as $car)
                                <option value="{{ $car->id }}">
                                    {{ $car->name }} ({{ $car->license_plate }}) - {{ $car->brand }} - {{ $car->seater }} seats - 
                                    {{ $car->is_available ? 'Available' : 'Not Available' }} 
                                </option>
                            @endforeach                        
                        </select>
                        <span id="car_id_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control form-control-sm" id="remarks" rows="3"></textarea>
                        <span id="remarks_error" class="text-danger d-none"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submit-button" type="submit" class="btn btn-primary">Submit Booking</button>
                    <button id="update-button" type="button" class="btn btn-secondary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
