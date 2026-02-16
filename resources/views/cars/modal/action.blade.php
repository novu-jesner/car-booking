<div class="modal fade" id="carModal" tabindex="-1" aria-labelledby="carModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="carModalLabel">Car Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="carForm" enctype="multipart/form-data">
                 @csrf 
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Car Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" id="name">
                            <span id="name_error" class="text-danger d-none"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_plate" class="form-label">License Plate</label>
                            <input type="text" name="license_plate" class="form-control form-control-sm" id="license_plate">
                            <span id="license_plate_error" class="text-danger d-none"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control form-control-sm" id="brand">
                            <span id="brand_error" class="text-danger d-none"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" name="type" class="form-control form-control-sm" id="type">
                            <span id="type_error" class="text-danger d-none"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="seater" class="form-label">Seater</label>
                            <input type="number" name="seater" class="form-control form-control-sm" id="seater">
                            <span id="seater_error" class="text-danger d-none"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_available" class="form-label">Available</label>
                            <select name="is_available" class="form-select form-select-sm" id="is_available">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <span id="is_available_error" class="text-danger d-none"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control form-control-sm" id="image">
                        <div id="image_preview" class="mt-2 d-none">
                            <img src="" alt="Car Preview" width="100" class="img-thumbnail">
                        </div>
                        <span id="image_error" class="text-danger d-none"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="update-button" type="button" class="btn btn-secondary" style="display: none;">Update</button>
                    <button id="submit-button" type="button" class="btn btn-secondary">Save</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>