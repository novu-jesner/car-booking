<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="updateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updateLabel">Service</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="myForm" >
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" id="email">
                        <span id="email_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" id="name">
                        <span id="name_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="employee_no" class="form-label">Employee No.</label>
                        <input type="text" name="employee_no" class="form-control form-control-sm" id="employee_no">
                        <span id="employee_no_error" class="text-danger d-none"></span>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-select form-select-sm" id="role">
                            <option value="" disabled selected>Select a role</option>
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <span id="role_error" class="text-danger d-none"></span>
                    </div>                                       
                </div>
                <div class="modal-footer">
                    <button id="update-button" type="button" class="btn btn-secondary">Update</button>
                    <button id="submit-button" type="button" class="btn btn-secondary">Add</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
