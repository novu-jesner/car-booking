@extends('layouts.app')

@section('content')
@include('cars.modal.action') 

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>Car Management</h3>
            <p>Manage your fleet of vehicles here.</p>
        </div>
        <button class="btn btn-primary btn-custom add-button">
            <i class="fa-solid fa-car"></i> Add Car
        </button>
    </div>
    <div class="table-responsive">
        <table class="table" id="carsTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>License Plate</th>
                    <th>Seater</th>
                    <th>Available</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>                
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
     $(document).ready(function () {
        // Helper to render divs (copied from UserManagement)
        var renderWithDiv = function(data, type, full, meta) {
            return '<div>' + data + '</div>';
        };

        // Initialize DataTable
        let dataTable = $('#carsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": '{{ route('cars.index') }}',
            "columns": [
                { "data": "car_image", "name": "image", "orderable": false, "searchable": false },
                { "data": "name", "name": "name", "render": renderWithDiv },
                { "data": "brand", "name": "brand", "render": renderWithDiv },
                { "data": "license_plate", "name": "license_plate", "render": renderWithDiv },
                { "data": "seater", "name": "seater", "render": renderWithDiv },
                { "data": "availability", "name": "is_available", "render": renderWithDiv },
                { "data": "actions", "name": "actions", "orderable": false, "searchable": false }
            ]
        });

        const myModal = $('#carModal');

        // Reset and Show Modal for Adding
        $('.add-button').click(e => {
            $('.modal-title').html('Add Car');
            $('#carForm')[0].reset();
            $('#submit-button').show();
            $('#update-button').hide();
            $('#image_preview').addClass('d-none'); 

            // Remove errors
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.form-select').removeClass('is-invalid');

            myModal.modal('show');
        });

        // Helper to handle Axios Errors
        function handleAxiosError(error) {
            if (error.response && error.response.status === 422) {
                $('.text-danger').addClass('d-none');
                $('.form-control').removeClass('is-invalid');
                $('.form-select').removeClass('is-invalid');

                $.each(error.response.data.errors, function(field, errorMessage) {
                    var errorSpanId = '#' + field + '_error';
                    $(`#${field}`).addClass('is-invalid');
                    $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
                });
            } else {
                Swal.fire({
                    title: "Oops!",
                    text: error.message || "An error occurred",
                    icon: "error"
                });
            }
        }

        // STORE (Create)
        $('#submit-button').click(e => {
            e.preventDefault(); 

            // Use FormData for file uploads
            const formData = new FormData($('#carForm')[0]);

            axios.post('{{ route('cars.store') }}', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                dataTable.ajax.reload();
                myModal.modal('hide');
                Swal.fire({
                    title: "Success!",
                    text: "Car added successfully.",
                    icon: "success"
                });
            })
            .catch(handleAxiosError);
        });

        // EDIT (Fetch Data)
        let id = null;
        $(document).on('click', '.edit-button', function() {
            id = $(this).attr('data-id');
            $('.modal-title').html('Edit Car');
            $('#submit-button').hide();
            $('#update-button').show();

            // Clear errors
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.form-select').removeClass('is-invalid');

            axios.get(`cars/${id}/edit`)
            .then((response) => {
                const data = response.data.data;
                
                // Populate fields
                $('#name').val(data.name);
                $('#license_plate').val(data.license_plate);
                $('#brand').val(data.brand);
                $('#type').val(data.type);
                $('#seater').val(data.seater);
                $('#is_available').val(data.is_available);

                // Handle Image Preview
                if(data.image) {
                    $('#image_preview img').attr('src', `{{ asset('storage') }}/${data.image}`);
                    $('#image_preview').removeClass('d-none');
                } else {
                    $('#image_preview').addClass('d-none');
                }

                myModal.modal('show');
            })
            .catch(error => {
                Swal.fire({ title: "Oops!", text: error.message, icon: "error" });
            });
        });

        // UPDATE
        $('#update-button').click(e => {
            e.preventDefault();

            const formData = new FormData($('#carForm')[0]);
            formData.append('_method', 'PUT'); 

            axios.post(`cars/${id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                dataTable.ajax.reload();
                myModal.modal('hide');
                Swal.fire({
                    title: "Success!",
                    text: "Car updated successfully.",
                    icon: "success"
                });
            })
            .catch(handleAxiosError);
        });

        // DELETE
        $(document).on('click', '.delete-button', function() {
            id = $(this).attr('data-id');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if(result.isConfirmed) {
                    axios.delete(`cars/${id}`)
                    .then(response => {
                        dataTable.ajax.reload();
                        Swal.fire({
                            title: "Deleted!",
                            text: "Car has been deleted.",
                            icon: "success"
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: "Oops!",
                            text: "Something went wrong!",
                            icon: "error"
                        });
                    })
                }
            })
        });
    });
</script>
@endsection