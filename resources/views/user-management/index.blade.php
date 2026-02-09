@extends('layouts.app')

@section('content')
@include('user-management.modal.edit')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>User Management</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum, nisi.</p>
        </div>
        <button class="btn btn-primary btn-custom add-button">
            <i class="fa-solid fa-user"></i> Add Account
        </button>
    </div>
    <div class="table-responsive">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee No.</th>
                    <th>Name</th>
                    <th>Role</th>
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
        var renderWithDiv = function(data, type, full, meta) {
            return '<div>' + data + '</div>';
        };
        let dataTable = $('#myTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": '{{ route('user-management.index') }}',
            "columns": [
                { "data": "profile_picture", "name": "profile_picture", "render": renderWithDiv },
                { "data": "employee_no", "name": "employee_no", "render": renderWithDiv },
                { "data": "name", "name": "name", "render": renderWithDiv },
                { "data": "role", "name": "role", "render": renderWithDiv },
                { "data": "actions", "name": "actions", "orderable": false, "searchable": false }
            ]
        });

        const myModal = $('#editModal');

        $('.add-button').click(e => {
            $('.modal-title').html('Add Account');
            $('#myForm')[0].reset();
            $('#submit-button').show();
            $('#update-button').hide();

            // Remove the previous error if there is
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');

            myModal.modal('show');
        })

        // Create
        $('#submit-button').click(e => {
            e.preventDefault(); 

            const formData = new FormData();
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('role', $('#role').val());
            formData.append('employee_no', $('#employee_no').val());

            axios.post('user-management', formData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                dataTable.ajax.reload();
                myModal.modal('hide');
                console.log(response)
                Swal.fire({
                    title: "Success!",
                    text: "Your data has been saved.",
                    icon: "success"
                });
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    $('.text-danger').addClass('d-none');
                    $(`.form-control`).removeClass('is-invalid');

                    $.each(error.response.data.errors, function(field, errorMessage) {
                        var errorSpanId = '#' + field + '_error';
                        $(`#${field}`).addClass('is-invalid');

                        // Show the error message in the respective error span
                        $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
                    });
                } else {
                    Swal.fire({
                        title: "Oops!",
                        text: error.message,
                        icon: "error"
                    });
                }
            });
        });

        // Edit show
        let id = null;
        $(document).on('click', '.edit-button', function() {
            id = $(this).attr('data-id');
            $('.modal-title').html('Edit Account');
            $('#submit-button').hide();
            $('#update-button').show();

             // Remove the previous error if there is
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            axios.get(`user-management/${id}/edit`)
            .then((response) => {
                const data = response.data.data;
                console.log(data);
                $('#name').val(data.name);
                $('#role').val(data.roles[0].name);
                $('#employee_no').val(data.employee_no);
                $('#email').val(data.email);
                myModal.modal('show');
            })
            .catch(error => {
                Swal.fire({
                    title: "Oops!",
                    text: error.message,
                    icon: "error"
                });
            })
        })

        // Update
        $('#update-button').click(e => {
            e.preventDefault();

            $('.text-danger').addClass('d-none');
            $(`#name`).removeClass('is-invalid');
            $(`#role`).removeClass('is-invalid');
            $(`#employee_no`).removeClass('is-invalid');
            $(`#email`).removeClass('is-invalid');

            const formData = new FormData();
            formData.append('name', $('#name').val());
            formData.append('role', $('#role').val());
            formData.append('employee_no', $('#employee_no').val());
            formData.append('email', $('#email').val());
            formData.append('_method', 'PUT');

            axios.post(`user-management/${id}`, formData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                dataTable.ajax.reload();
                myModal.modal('hide');
                Swal.fire({
                    title: "Success!",
                    text: "Your changes have been saved.",
                    icon: "success"
                });
            })
            .catch(error => {
                if(error.response && error.response.status === 422) {
                    // Clear previous error messages
                    $('.text-danger').addClass('d-none');
                    $('.text-danger').addClass('d-none');
                    $(`#name`).removeClass('is-invalid');
                    $(`#role`).removeClass('is-invalid');
                    $(`#employee_no`).removeClass('is-invalid');
                    $(`#email`).removeClass('is-invalid');

                    $.each(error.response.data.errors, function(field, errorMessage) {
                        var errorSpanId = '#' + field + '_error';
                        $(`#${field}`).addClass('is-invalid');

                        // Show the error message in the respective error span
                        $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
                    });

                } else {
                    Swal.fire({
                        title: "Oops!",
                        text: error.message,
                        icon: "error"
                    });
                }
            });
        });

         // Delete
         $(document).on('click', '.delete-button', function() {
            id = $(this).attr('data-id');

            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if(result.isConfirmed) {
                    axios.delete(`user-management/${id}`)
                    .then(response => {
                        dataTable.ajax.reload();
                        console.log(response.data.data)
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your data has been deleted.",
                            icon: "success"
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: "Oops!",
                            text: "Something went wrong, try again later!",
                            icon: "error"
                        });
                    })
                }
            })
        })
    })
</script>
@endsection